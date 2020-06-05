const addressData = require('china-area-data/v4/data')
Vue.component('user_address', {
    props: {
        initValue: {
            type: Array,
            default: () => ([])
        },
    },
    data() {
        return {
            provinces: addressData['86'],
            cities: {},
            districts: {},
            provinceId: '',
            cityId: '',
            districtId: '',
        };
    },
    watch: {
        provinceId(value) {
            if(!value){
                return;
            }
            this.cities=addressData[value];
        },
        cityId(value) {
            if(!value){
                return;
            }
            this.districts=addressData[value];

        },
        districtId(value) {
            console.log(value)
        },
        districtId(){
            this.$emit('change',[this.provinces[this.provinceId],this.cities[this.cityId],this.districts[this.districtId]]);
        }
    },
});
