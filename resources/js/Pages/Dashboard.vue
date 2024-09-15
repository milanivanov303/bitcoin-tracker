<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="card">
                        <div class="col s12 l6 right">
                            <Select
                                v-model="currency"
                                :options="currencyOptions"
                                label="Currency"
                                :defaultOption="true"
                                defaultOptionText="Select currency"
                            />
                            <Select
                                v-model="timeRange"
                                :options="timeRangeOptions"
                                label="Time Range"
                                :defaultOption="false"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="!loaded" class="spinner-container">
                        <div class="spinner"></div>
                    </div>

                    <div v-if="timeRange === 'day'" class="date-controls">
                        <button @click="changeDate(-1)" class="custom-card">Previous</button>
                        <span class="custom-card mr-2 ml-2">{{ displayDate }}</span>
                        <button v-if="!today" @click="changeDate(1)" class="custom-card">Next</button>
                    </div>

                    <Chart v-if="loaded" :data="chartData" :options="chartOptions" />        
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="card custom-card">
                        <div class="row">
                            <div class="col s5">
                                <SubscriptionForm :alertType="priceAlertType" :title="priceAlertTitle"/>
                            </div>
                            <div class="col s5 mr-1 right">
                                <SubscriptionForm :alertType="percentAlertType" :title="percentAlertTitle"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Chart from '@/Components/Chart.vue';
import Select from '@/Components/Select.vue';
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import SubscriptionForm from '@/Components/SubscriptionForm.vue';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Chart,
        Select,
        SubscriptionForm
    },
    setup() {
        const priceAlertTitle = 'Subscribe to Price Alerts';
        const priceAlertType = 'price';
        const percentAlertTitle = 'Subscribe to Percent jump/drop Alerts';
        const percentAlertType = 'percent';
        const email = ref('');
        const priceLimit = ref(0);
        const currency = ref('tBTCUSD');
        const timeRange = ref('24h');
        const loaded = ref(false);
        const today = ref(true);
        const currentDate = ref(new Date());
        const displayDate = ref(currentDate.value.toLocaleDateString('en-GB'));

        const currencyOptions = ref([
            { name: 'USD', value: 'tBTCUSD' },
            { name: 'EUR', value: 'tBTCEUR' }
        ]);

        const timeRangeOptions = ref([
            { name: 'Last 24 hours', value: '24h' },
            { name: 'Day View', value: 'day' },
            { name: 'Week View', value: 'week' },
        ]);

        const chartData = ref({
            labels: [],
            datasets: [{
                label: 'Bitcoin Price (USD)',
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false,
                data: []
            }]
        });

        const chartOptions = ref({
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Bitcoin Price Log'
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Time'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Bitcoin Price Linear'
                    }
                }
            }
        });

        const subscribe = async () => {
            try {
                const response = await axios.post('/tracker/subscribe', {
                    email: email.value,
                    price: priceLimit.value,
                });

                alert(response.data.message);
            } catch (error) {
                console.error('Error subscribing:', error);
                alert('Failed to subscribe. Please try again.');
            }
        };

        const updateDisplayDate = () => {
            displayDate.value = currentDate.value.toLocaleDateString('en-GB');
        };

        const isToday = () => {
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);
            const current = new Date(currentDate.value);
            current.setHours(0, 0, 0, 0);
            today.value = todayDate.getTime() === current.getTime();
        };

        const changeDate = (days: number) => {
            currentDate.value.setDate(currentDate.value.getDate() + days);
            updateDisplayDate();
            isToday();
            fetchData();
        };

        const getStartOfWeek = () => {
            const now = new Date();
            const dayOfWeek = now.getDay(); 
            const diffToMonday = (dayOfWeek === 0 ? 6 : dayOfWeek - 1);
            now.setHours(0, 0, 0, 0);
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - diffToMonday);

            return startOfWeek.getTime();
        };

        const getTimeRange = () => {
            const now = new Date();
            let start, end;

            switch (timeRange.value) {
                case '24h':
                    start = now.getTime() - 24 * 60 * 60 * 1000;
                    end = now.getTime();
                    break;
                case 'day':
                    const dayStart = new Date(currentDate.value);
                    dayStart.setHours(0, 0, 0, 0);
                    const dayEnd = new Date(dayStart);
                    dayEnd.setDate(dayEnd.getDate() + 1);
                    start = dayStart.getTime();
                    end = dayEnd.getTime();
                    break;
                case 'week':
                    start = getStartOfWeek();
                    end = now.getTime();
                    break;
                default:
                    throw new Error('Invalid time range');
            }

            return { start, end };
        };

        const fetchData = async () => {
            try {
                loaded.value = false;
                const now = new Date();
                const { start, end } = getTimeRange();

                const response = await axios.get('/tracker/candles', {
                    params: {
                        symbol: currency.value,
                        start,
                        end,
                    }
                });

                const candles = response.data;
                const labels = timeRange.value === 'week' ?
                    candles.map((candle: any) => new Intl.DateTimeFormat('en-GB', {
                        weekday: 'short',   // Tue
                        year: 'numeric',    // 2024
                        month: 'short',     // Sep
                        day: 'numeric',     // 10
                        hour: '2-digit',    // hh
                        minute: '2-digit',  // mm
                        hour12: false       // 24-hour format
                    }).format(new Date(candle.timestamp))) :
                    candles.map((candle: any) => new Date(candle.timestamp).toLocaleTimeString('en-GB'));
                const data = candles.map((candle: any) => candle.closingPrice);

                chartData.value.labels = labels;
                chartData.value.datasets[0].data = data;

                const selectedCurrency = currencyOptions.value.find(option => option.value === currency.value);
                chartData.value.datasets[0].label = selectedCurrency ? `Bitcoin price (${selectedCurrency.name})` : 'Bitcoin Price';

                loaded.value = true;
            } catch (error) {
                console.error('Error fetching candles data:', error);
            }
        };

        watch([currency, timeRange], fetchData);

        onMounted(() => {
            fetchData();
            updateDisplayDate();
        });

        return {
            currencyOptions,
            timeRangeOptions,
            currency,
            timeRange,
            chartData,
            chartOptions,
            loaded,
            fetchData,
            changeDate,
            displayDate,
            email,
            priceLimit,
            subscribe,
            priceAlertTitle,
            percentAlertTitle,
            percentAlertType,
            priceAlertType,
            today
        };
    }
}
</script>

<style scoped>
.spinner-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px;
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-left-color: rgb(75, 192, 192);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}

.date-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
}

.custom-card {
    background-color: #41333338;
    color: rgb(75, 192, 192);
}
</style>
