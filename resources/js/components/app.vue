<template>
    <div class="justify-content-center text-center mt-5">
        <h3>Welcome to Catering Payment System, Inya Lake Hotel</h3>
    </div>
    <div class="container mt-5 justify-content-center text-center">
   <h5>{{ current_user.name }}, your role is {{ role_name }}</h5> 
    </div>
</template>

<script>
export default {
    setup: () => ({
        title: ''
    }),
    data() {
        return {
            role_name: { },

            current_user: {},

        }
    },
    created(){
        console.log(window.user)
        this.current_user = window.user
        if(this.current_user.role==1){
            this.role_name = 'Admin'
        }else if(this.current_user.role==2){
            this.role_name = 'FO'
        }else{
            this.role_name = 'User'
        }
    },
}
</script>