<template>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-light">
                    <div class="row">
                        <div class="col-md-12" v-for="eventtype in eventtypes">
                                <h5 v-if="eventtype.id==form.id" style="text-align: center;">{{ eventtype.name }}</h5>
                                
                                </div>
                        </div>
                    </div>
                        <div class="card-body">
                    <div class="shadow p-3 mb-5 bg-body rounded">

                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>EventType</th>                              
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(event, index) in eventfacilities " :key="index" class="bg-transparent">
                                <td>{{index + 1}}</td>                                
                                <td>{{event.facility_name}}</td>
                              
                               
                                <td><button @click="removeLink(event)" class="btn btn-danger btn-sm mx-1">Del</button></td>
          
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-light">
                    <div class="row">
                       
                        <div class="col-md-12">
                            <input class="form-control-sm rounded" type="text" v-model="keyword" placeholder="search ..">
                            
                            <button @click="Create"  class="btn-info btn-sm float-end mx-2">New</button>
                        
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="shadow p-3 mb-5 bg-body rounded">

                        <table class="table">
                            <thead>
                            <tr>                               
                                <th>Facilities</th>                              
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(event, index) in facilities " :key="index" class="bg-transparent">                                                     
                                <td>{{event.facility_name}}</td>
                              
                               
                                <td><button @click="AddFacility(event)" class="btn btn-primary btn-sm mx-1">Add</button></td>
          
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

       <!-- Modal -->
       <div class="modal fade" id="facilityModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
        <div :class="`modal-dialog ${!deleteMode ? 'modal-lg': 'modal-sm'}`">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="bookModalLabel" v-show="!deleteMode && !renewMode"> {{!editMode ? 'Add New Facility': 'Update Deposit' }} </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="deleteMode && !confirmMode && !unconfirmMode" > Delete Booking </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="deleteMode && confirmMode && !unconfirmMode" > Confirm Booking </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="deleteMode && !confirmMode && unconfirmMode" > It's not available </h5>
                    <h5 class="modal-title" id="bookModalLabel" v-show="renewMode" >Renew License </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" v-show="!deleteMode && !renewMode">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title" >Descriptions.</label>
                                <!-- <input type="hidden" v-model="eventData.id"> -->
                                <input type="text" class="form-control" v-model="facilityData.facility_name">

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
                    <button type="button" class="btn btn-primary" @click="!editMode ? storeFacility(): updateDeposit()" >{{!editMode ? 'Add Facility': 'Save Changes' }}</button>
                </div>
                <div class="modal-footer" v-show="deleteMode && !confirmMode && !unconfirmMode">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" @click="deleteLink" >Delete</button>
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
        title: 'Facilities'
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
            form: { id : ''},
            bookData: {
                destination: '',
                duation: '',
                date: '',
                time: '',
                confirm: '',

            },
            facilityData: {
                id: "",

            },
            // report:{
            //     from_date: '',
            //     to_date: '',
            // },

            events: {},
            eventtypes: {},
            facilities: {},
            eventfacilities: {},
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
        this.getFacilities()
    },

    created(){
        console.log(window.user)
        this.form.id = this.$route.params.id
        this.current_user = window.user
        axios.get( '/api/getFacilities/')
            .then(({data}) => (this.facilities = data))
        axios.get( '/api/getEventtypes/')
            .then(({data}) => (this.eventtypes = data))
        axios.get( '/api/getPackages/')
            .then(({data}) => (this.packages = data))
    },
    methods: {
        format_date(value){
         if (value) {
           return moment(String(value)).format('DD-MM-YY')
          }
        },

        getFacilities(){

            axios.get('/api/getFacilities/'+this.form.id,{ params: { keyword: this.keyword } }).then(response=>{
                this.eventfacilities = response.data
            }).catch(errors=>{
                console.log(errors)
            });
            if(this.current_user.role==2){
                this.foMode = true
            }


        },

        removeLink(event){
            this.deleteMode = true
            this.confirmMode = false
            this.confirmMode = false
            this.facilityData = {facility_id : event.id}
           
                $('#facilityModal').modal('show')
            

        },

        deleteLink(){
            axios.post(window.url + 'api/deletelink/' + this.form.id,this.facilityData).then(response => {
                this.getFacilities()
            }).catch(errors => {
                console.log(errors)
            }).finally(() => {
                $('#facilityModal').modal('hide')
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
        AddFacility(event){

            this.facilityData = { id : event.id }
                axios.post(window.url +'api/eventfacility/'+this.form.id,this.facilityData).then(response => {
                    this.getFacilities()
                }).catch(errors => {
                    console.log(errors)
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
        Create(){
            this.editMode = false
            this.deleteMode = false
            this.facilityData= {
                facility_name: '',
              

            }

            $('#facilityModal').modal('show')
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

        storeFacility(){



                axios.post('/api/storeFacility', this.facilityData).then(response=>{
                    axios.get( '/api/getFacilities/')
            .then(({data}) => (this.facilities = data))
                    }).catch(errors=>{
                        console.log(errors)
                    }).finally(()=>{
                        $('#facilityModal').modal('hide')
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
