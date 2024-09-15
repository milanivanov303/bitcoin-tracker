<template>
    <div class="subscription-form">
        <h6>{{ title }}</h6>
        
            <div class="card custom-card">
                <form @submit.prevent="subscribe">
                    <div class="row">
                        <div class="col s12">
                            <label for="email">Email:</label>
                            <input type="email" v-model="email" id="email" required />
                        </div>
                    </div>

                    <div class="row" v-if="alertType === 'price'">
                        <div class="col s12">
                            <label for="price">Price Limit:</label>
                            <input type="number" v-model="priceLimit" id="price" class="input-field" required />
                        </div>
                    </div>

                    <div class="row" v-if="alertType === 'percent'">
                        <div class="col s12">
                            <label for="percent">Percent Change:</label>
                            <input v-model="percent" id="percent" class="input-field" placeholder="10" pattern="^\d+(\.\d{1,2})?$" required />
                        </div>
                    </div>

                    <div class="row" v-if="alertType === 'percent'">
                        <div class="col s12">
                            <Select 
                            v-model="selectedTimeInterval" 
                            :options="timeIntervalOptions" 
                            label="Time Interval" 
                            :defaultOption="false" 
                            />
                        </div>
                    </div>
                    
                    <button type="submit">Subscribe</button>
                </form>
            </div>
    </div>
</template>

<script lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import Select from '@/Components/Select.vue';

export default {
    name: 'SubscriptionForm',
    components: {
        Select,
    },
    props: {
        alertType: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            default: ''
        },
    },
    setup(props) {
        const email = ref('');
        const priceLimit = ref(0);
        const percent = ref(0);
        const selectedTimeInterval = ref('1h');
        const timeIntervalOptions = ref([
            { name: '1 hour', value: '1h' },
            { name: '6 hours', value: '6h' },
            { name: '24 hours', value: '24h' },
        ]);

        const resetFields = () => {
            email.value = '';
            priceLimit.value = 0;
            percent.value = 0;
            selectedTimeInterval.value = '1h';
        };

        const subscribe = async () => {
            try {
                const payload: Record<string, any> = {
                    type: props.alertType,
                    email: email.value,
                };

                if (props.alertType === 'price') {
                    payload.price = priceLimit.value;
                } else if (props.alertType === 'percent') {
                    payload.percent = percent.value;
                    payload.timeInterval = parseInt(selectedTimeInterval.value, 10);
                }

                const response = await axios.post('/tracker/subscribe', payload);

                alert(response.data.message);
                resetFields();
            } catch (error) {
                const errorMessage = axios.isAxiosError(error)
                    ? error.response?.data?.message || 'Failed to subscribe. Please try again.'
                    : 'Failed to subscribe. Please try again.';

                console.error('Error subscribing:', error);
                alert(errorMessage);
            }
        };

        return {
            email, 
            priceLimit, 
            percent,
            selectedTimeInterval,
            timeIntervalOptions,
            subscribe,
        };
    }
};
</script>

<style scoped>
.subscription-form {
  margin-top: 20px;
}

.subscription-form label {
  display: block;
  margin-bottom: 5px;
}

.subscription-form .input-field {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.subscription-form button {
  padding: 10px 20px;
  background-color: rgb(29, 90, 90);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.subscription-form button:hover {
  background-color: rgb(75, 192, 192);
}

.custom-card {
  background-color: #41333338;
  color: rgb(75, 192, 192);
}
</style>
