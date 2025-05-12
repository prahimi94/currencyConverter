
<template>
    <div class="BG">    

    <dotlottie-player
      :src="BGAnimation"
      background="transparent"
      speed="1"
      style="width: 100%;"
      loop
      autoplay
      class="animetion"
      ></dotlottie-player>
  </div>

  <div class="container">
    <div class="card">
      <h1 class="title">Exchange</h1>
      <div>
        <input class="input" type="text" placeholder="Amount" ref="amount"/>
      </div>
      <div>
        <select v-model="fromCurrency" id="currenciesFrom" placeholder="FROM ..." class="select-form">
          <option disabled value="">FROM ...</option>
          <option v-for="option in options" :key="option.id" :value="option.id">
            {{ option.name }}
          </option>
        </select>
      </div>
      <div>
        <select v-model="toCurrency" id="currenciesTo" placeholder="TO ..." class="select-form">
          <option disabled value="">To ...</option>
          <option v-for="option in options" :key="option.id" :value="option.id">
            {{ option.name }}
          </option>
        </select>
      </div>
      <div>
        <input class="input" type="text" readonly ref="result" placeholder="Result" />
        <button class="btn" @click="convert">Convert</button>
      </div>
    </div>
  </div>
  

  <div class="copyright">© 2025 Parisa, Created by <span>❤️ ☕</span></div>
  </template>
  
  <script>
  import { useToast } from 'vue-toastification';

  const toast = useToast();

  export default {
    
    name: 'App',
    data: function () {
      return {
        fromCurrency: '',
        toCurrency: '',
        options: [],
        reduce: (option) => option.id,
        BGAnimation: '/images/BG.lottie'
    };
    },
    mounted() {
      axios.get('/api/rest/currencies')
        .then(response => {
          response = response.data;
          if(!response.success) {
            toast.error('Failed to fetch currencies: ' + response);
            return;
          }

          this.options = response.data.map(item => ({
            id: item.code,
            name: item.name,
          }));
        })
        .catch(error => {
          toast.error('Failed to fetch currencies: ' + error);
        });
    },
    methods: {
    async convert() {
      const amount = this.$refs.amount.value;

      if (!amount || !this.fromCurrency || !this.toCurrency) {
        toast.error("Please fill in all fields");
        return;
      }

      try {
        this.$refs.result.value = '';
        const response = await axios.post('/api/rest/convert', {
              amount,
              from: this.fromCurrency,
              to: this.toCurrency
            });

        if (!response.data.success) {
          toast.error('Failed to convert currencies: ' + response.data.message);
          return;
        }
        toast.success('Data fetched successfully');

        const userLocale = navigator.language || 'en-US';
        
        const convertedAmount = response.data.data; 
        if(!convertedAmount) {
          toast.error('No result');
          return;
        } else {
          const formatted = new Intl.NumberFormat(userLocale, {
            style: 'currency',
            currency: this.toCurrency,
          }).format(convertedAmount);

          this.$refs.result.value = formatted;
        }
      } catch (error) {
        console.log(error.response.data.message); 
        if (error.response.data.message) {
          toast.error(error.response.data.message);
        } else {
          toast.error('Error occurred while converting currencies');
        }
      }
    }
  }
  };
  </script>