Vue.component('user_address_add_and_edit', {
    data() {
        return {
            province:'',
            city:'',
            district:'',
        };
    },
    methods:{
        onDistrictChanged(value){
            if(value.length === 3){
                this.province=value[0];
                this.city=value[1];
                this.district=value[2];
            }
        }
    },
});
