<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-transparent">
                <div class="card-header bg-primary text-light">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="float-start">{{ title }}</h5>
                        </div>
                        <div class="col-md-4 float-center">
                            <h5>Hi! {{ current_user.name }}</h5>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control-sm rounded" type="text" v-model="keyword" placeholder="search event..">
                            <div v-show="foMode">
                            <button @click="report"  class="btn-info btn-sm float-end mx-2">Report by Date</button>
                        </div>
                        </div>
                    </div>

                </div>
                <div class="card-body bg-transparent">
                    <div class="shadow p-3 mb-5 rounded">

                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>EventName</th>
                                <th>EventDate</th>
                                <th>Time</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(event, index) in events " :key="index" class="bg-transparent">
                                <td>{{index + 1}}</td>                                
                                <td>{{event.name}}</td>
                                <td>{{event.event_date}}</td>
                                <td>{{event.event_time}}</td>                               
                                <td>{{ event.currency }} {{event.amount}}</td>

                                <!-- <td><div v-for="packagge in packages"><div  v-if="event.event_id==packagge.event_id">{{ packagge.name }}</div></div></td>
                                <td>{{event.eholderName}} {{ event.mobile }}</td> -->
                                <td><button @click="print(event)" class="btn btn-primary btn-sm mx-1">Receipt</button></td>
          
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

       <!-- Modal -->
       <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
        <div :class="`modal-dialog ${!deleteMode ? 'modal-lg': 'modal-sm'}`">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="bookModalLabel" v-show="!deleteMode && !renewMode"> {{!editMode ? 'Add New Booking': 'Update Deposit' }} </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="deleteMode && !confirmMode && !unconfirmMode" > Delete Booking </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="deleteMode && confirmMode && !unconfirmMode" > Confirm Booking </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="deleteMode && !confirmMode && unconfirmMode" > It's not available </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="renewMode" >Renew License </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" v-show="!deleteMode && !renewMode">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="title" >Destination.</label>
                                <input type="hidden" v-model="eventData.id">
                                <input type="text" class="form-control" v-model="eventData.name">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date" >Date</label>
                                <input type="date" class="form-control" v-model="eventData.date">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="color" >Time</label>
                                <input type="text" class="form-control" v-model="eventData.time">

                            </div>
                        </div>
                    </div>

                    <div class="row" v-show="!deleteMode && !renewMode">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reg_no" >Payment Type</label>
                                <select name="cars" class="form-control" v-model="eventData.payment_type">                          
                                <option value="Deposit">Deposit</option>
                                <option value="Final">Final</option>                              
                                </select>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reg_no" >Currency</label>
                                <select name="cars" class="form-control" v-model="eventData.currency">                          
                                <option value="MMK">MMK</option>
                                <option value="USD">USD</option>                              
                                </select>

                            </div>
                        </div>

                    
                   
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reg_no" >Deposit Amount</label>
                                <input type="text" class="form-control" v-model="eventData.amount">

                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="!deleteMode && !renewMode">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="reg_no" >Note</label>
                                <input type="text" class="form-control" v-model="eventData.note">
                            </div>
                        </div>
                     
                    </div>
                   
                    <div class="row" v-show="deleteMode && confirmMode && !unconfirmMode">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title" >Assign Driver</label>
                                <select class="form-control"  v-model="assign.driver_id">

                                    <option :value="driver.id"  v-for="driver in drivers">{{ driver.name }}</option>

                                </select>
                                <small class="text-danger" v-if="errors.driver_id"> {{ errors.driver_id[0] }} </small><br>
                            </div>
                        </div>

                    </div>


                <!-- <div class="row mt-2" v-show="renewMode">
                <div class="form-group">
                <label for="title" >Report From</label>
                <input type="date" class="form-control" v-model="report.from_date">
                <label for="title" >To</label>
                <input type="date" class="form-control" v-model="report.to_date">

                <button type="button" class="btn btn-primary mt-1" @click="reportFuel()" >Generate</button>
                </div>
                </div> -->


                        <h4 class="text-center" v-show="deleteMode && !confirmMode && !unconfirmMode">Are you sure want to delete!</h4>
                        <h4 class="text-center" v-show="deleteMode && confirmMode && !unconfirmMode">Are you sure want to change status as Confirm</h4>
                        <h4 class="text-center" v-show="deleteMode && !confirmMode && unconfirmMode">Are you sure want to change status as not available</h4>

                </div>
                <div class="modal-footer" v-show="!deleteMode && !renewMode">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="!editMode ? storeBook(): updateDeposit()" >{{!editMode ? 'Book': 'Save Changes' }}</button>
                </div>
                <div class="modal-footer" v-show="deleteMode && !confirmMode && !unconfirmMode">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" @click="deleteBook" >Delete</button>
                </div>
                <div class="modal-footer" v-show="deleteMode && confirmMode && !unconfirmMode">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" @click="confirmedBook" >Yes!!!</button>
                </div>
                <div class="modal-footer" v-show="deleteMode && !confirmMode && unconfirmMode">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" @click="unconfirmedBook" >Yes!!!</button>
                </div>
                <div class="modal-footer" v-show="renewMode">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" @click="renewCar" >Renew</button>
                </div>
            </div>
        </div>
           
    </div>
             <!-- Modal -->
             <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Report by Date range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                <label for="title" >Report From</label>
                <input type="date" class="form-control" v-model="report.from_date">
                <label for="title" >To</label>
                <input type="date" class="form-control" v-model="report.to_date">



               
            </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="reportBooking()">Generate</button>
                </div>
                </div>
            </div>
            </div>
    

</template>

<script>
    import moment from 'moment';
export default {
    setup: () => ({
        title: 'Payments'
    }),
    data() {
        return {
            foMode: false,
            editMode: false,
            deleteMode: false,
            renewMode:false,
            confirmMode: false,
            unconfirmMode: false,
            keyword: null,
            bookData: {
                destination: '',
                duation: '',
                date: '',
                time: '',
                confirm: '',

            },
            eventData: {
                

            },
            // report:{
            //     from_date: '',
            //     to_date: '',
            // },

            events: {},
            errors: {},
            current_user: {},
            drivers:{},
            assign:{},
            packages:{},
        }
    },
    watch: {
        keyword(after, before) {
            this.getEvents();
        }
    },
    mounted(){
        this.getEvents()
    },
    created(){
        console.log(window.user)
        this.current_user = window.user
        axios.get( '/api/getDrivers/')
            .then(({data}) => (this.drivers = data))
        axios.get( '/api/getPackages/')
            .then(({data}) => (this.packages = data))
    },
    methods: {
        format_date(value){
         if (value) {
           return moment(String(value)).format('DD-MM-YY')
          }
        },

        getEvents(){

            axios.get('api/getPayments',{ params: { keyword: this.keyword } }).then(response=>{
                this.events = response.data
            }).catch(errors=>{
                console.log(errors)
            });
            if(this.current_user.role==2){
                this.foMode = true
            }


        },

        removeBook(book){
            this.deleteMode = true
            this.confirmMode = false
            this.confirmMode = false
            this.bookData.id = book.id
            if(this.current_user.id==book.user_id){
                $('#bookModal').modal('show')
            }

        },

        deleteBook(){
            axios.post(window.url + 'api/deleteBook/' + this.bookData.id).then(response => {
                this.getBooks()
            }).catch(errors => {
                console.log(errors)
            }).finally(() => {
                $('#bookModal').modal('hide')
            });
        },
        confirmBook(book){
            this.deleteMode = true
            this.confirmMode = true
            this.unconfirmMode = false
            this.bookData.id = book.id
            $('#bookModal').modal('show')
        },
        unconfirmBook(book){
            this.deleteMode = true
            this.confirmMode = false
            this.unconfirmMode = true
            this.bookData.id = book.id
            $('#bookModal').modal('show')
        },
        confirmedBook(){
            axios.post(window.url + 'api/confirmBook/' + this.bookData.id,this.assign).then(() => {
                $('#bookModal').modal('hide');
                this.getBooks()
            }).catch(error =>this.errors = error.response.data.errors)

        },
        unconfirmedBook(){
            axios.post(window.url + 'api/unconfirmBook/' + this.bookData.id).then(response => {
                this.getBooks()
            }).catch(errors => {
                console.log(errors)
            }).finally(() => {
                $('#bookModal').modal('hide')
            });
        },

        renew(book){
            this.renewMode = true
            this.deleteMode = false
            this.editMode = false

            this.bookData= {
                id : book.id,
                renew: book.renew,

            }
            $('#bookModal').modal('show')

        },
        editBook(book){
            this.editMode = true
            this.deleteMode= false
            this.bookData= {
                id : book.id,
                destination: book.destination,
                date: book.date,
                time: book.time,
                duation: book.duation,
                confirm: book.confirm,

            }

            if(this.current_user.id==book.user_id){
                $('#bookModal').modal('show')
            }
        },
        depositPayment(event){
            this.editMode = true
            this.deleteMode= false
            this.eventData= {
                id : event.id,
                name :event.name,
                date : event.date,
                time: event.time,
               
            }
            this.paymentErrors= {
                name: false,
                phone: false,

            }
            $('#paymentModal').modal('show')
        },
        updateDeposit(){



            axios.post('api/updateDeposit/' + this.eventData.id, this.eventData).then(response => {
                this.getEvents()
            }).catch(errors => {
                console.log(errors)
            }).finally(() => {
                $('#paymentModal').modal('hide')
            });


            // axios({
            // method:'post',
            // url:'api/updatePayment/' + this.paymentData.id,
            // responseType:'arraybuffer',
            // data: 'test'
            // })
            // .then(function(response) {
            //     let blob = new Blob([response.data], { type:   'application/pdf' } );
            //     let link = document.createElement('a');
            //     link.href = window.URL.createObjectURL(blob);
            //     link.download = 'Report.pdf';
            //     link.click();
            // });

        },
        print(event){
           this.eventData.id = event.id
            axios({
                method:'post',
                url:'/api/receiptForm/' + this.eventData.id,
                responseType:'arraybuffer',

            })
                .then(function(response) {
                    let blob = new Blob([response.data], { type:   'application/pdf' } );
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download ="receipt.pdf";
                    link.click();
                });

        },
        updateBook(){

                axios.post(window.url + 'api/updateBook/' + this.bookData.id, this.bookData).then(response => {
                    this.getBooks()
                }).catch(errors => {
                    console.log(errors)
                }).finally(() => {
                    $('#bookModal').modal('hide')
                });

        },
        renewCar(){

                axios.post(window.url + 'api/renewCar/' + this.bookData.id, this.bookData).then(response => {
                    this.getBooks()
                }).catch(errors => {
                    console.log(errors)
                }).finally(() => {
                    $('#bookModal').modal('hide')
                });

        },
        createBook(){
            this.editMode = false
            this.deleteMode = false
            this.bookData= {
                destination: '',
                duation: '',
                date: '',
                time: '',
                confirm: '',

            }

            $('#bookModal').modal('show')
        },
        report(){
            this.report={
                from_date:'',
                to_date:'',
            }
           

            $('#reportModal').modal('show')
        },
        reportBooking(){
      
                   axios({
                method:'post',
                url:'/api/reportBooking',
                responseType:'arraybuffer',
                data: this.report
                })
                .then(function(response) {
                    let blob = new Blob([response.data], { type:   'application/pdf' } );
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Report.pdf';
                    link.click();
                    $('#reportModal').modal('hide')
                });
     },

        storeBook(){



                axios.post('api/storeBook', this.bookData).then(response=>{
                    this.getBooks()
                    }).catch(errors=>{
                        console.log(errors)
                    }).finally(()=>{
                        $('#bookModal').modal('hide')
                });



        }
    }

}
</script>

<style>
.hide {
    display: none;
    font-size: 11px;
}

.myDIV:hover + .hide {
    display: block;
    color: blue;
}
.owner {
    font-weight: bold;
}

</style>
