
 <!-- <v-col>   showing an msg that Usercomponent is loaded
        <v-alert type="info">users Component loaded Successfully</v-alert>
   </v-col>
   -->
<template>
 <v-app id="inspire">
  <v-data-table
    item-key="name"
    class="elevation-1"
    :loading="loading"
    loading-text="Loading... Please wait"
    :headers="headers"
    @pagination="paginate"
    :server-items-length="users.total"
    :items="users.data"
    :items-per-page=5
    show-select
    @input="selectAll"
    :footer-props="{
        itemsPerPageOptions:[ 5,10,15],
        itemsPerPageText: 'users Per Page',
        showCurrentPage :true ,
        showFirstLastPage:true,
    }"
  >
    <template v-slot:top>
      <v-toolbar flat color="dark">
        <v-toolbar-title>User Management System</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on }">
            <v-btn color="error darken-1" dark class="mb-2" v-on="on">Add New User</v-btn>
             <v-btn color="error darken-1" dark class="mb-2 mr-2" @click="deleteAll" >Delete</v-btn>
          </template>
          <v-card>
              <v-form v-model="valid" method="post" v-on:submit.stop.prevent="save" >
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12" sm="12">
                    <v-text-field color="error" v-model="editedItem.name" label="User Name" :counter="15" :rules="[rules.required , rules.min , rules.max , rules.alphaNumeicOnly]"></v-text-field>
                  </v-col>
                  </v-row>
                  <v-row v-if="editedIndex == -1">
                  <v-col cols="12" sm="12">
                    <v-text-field color="error" type="email" @blur="checkEmail" :success-messages="success" :error-messages="error"  v-model="editedItem.email" label="Email" :rules="[rules.required , rules.validEmail]"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12">
                    <v-text-field color="error" type="password" v-model="editedItem.password" label="Password" :counter="15" :rules="[rules.required ,rules.max ]"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12">
                    <v-text-field color="error" type="password" v-model="editedItem.rpassword" label="ReType Password" :rules="[rules.required ,passwordMatch]"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12">
                    <v-text-field color="error"   v-model="editedItem.phone_no" label="Phone No" :rules="[rules.required , rules.NumeicOnly]"></v-text-field>
                  </v-col>
                  </v-row>
                  <v-row>
                  <v-col cols="12" sm="12">
                    <v-select :items="roles" color="error" label="Role" :rules="[rules.required]" v-model="editedItem.role"></v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" text @click="close">Cancel</v-btn>
              <v-btn type="submit" :disabled="!valid" @click.prevent="save" color="error" text >Save</v-btn>
            </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>
      </v-toolbar>
             <v-row>
                <v-col  cols="12" sm="6" >
                    <v-text-field class="mx-4" color="error" append-icon="mdi-magnify" @input="search_it" label="Search....."></v-text-field>
                </v-col>
            </v-row>
    </template>
    <template v-slot:item.role ="{ item }">
        <v-edit-dialog
          large
          block
          presistent
          return-value.sync="item.role"
          @save="updateRole(item)"

        >
            {{item.role}}
            <template v-slot:input>
                <br>
                <h3>Update User's Role</h3>
                <br>
                <v-select :items="roles" color="error" label="Role" :rules="[rules.required]" v-model="item.role"></v-select>
            </template>

        </v-edit-dialog>
    </template>
    <template v-slot:item.photo="{ item }">
        <v-edit-dialog  >
        <v-list-item-avatar>
        <v-img
      :src="item.photo"
      aspect-ratio="1"
      class="grey lighten-2"
        />
        </v-list-item-avatar>
        <template v-slot:input>
            <v-file-input
            v-model="editedItem.photo"
            accept="image/jpg, image/png, image/bmp, image/jpeg"
            label="Select File"
            placeholder="Uplaod Avatar"
            prepend-icon="mdi-camera"
            @change="uploadPhoto(item)"
            />
        </template>
        </v-edit-dialog>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
      <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn color="error" @click="initialize">Reset</v-btn>
    </template>
  </v-data-table>
            <v-snackbar v-model="snackbar">
              {{ snackbarText }}
              <v-btn color="error" text @click="snackbar = false">Close</v-btn>
            </v-snackbar>
  </v-app>
</template>

<script>
export default {
  data: () => ({
      valid: true,
    dialog: false,
    loading: false,
    snackbar:false,
    snackbarText: '',
    success: '',
    error: '',
    selected:[],
    roles:[],
    rules:{
         required: v => !!v || 'This Feild is Required',
         alphaNumeicOnly: v => /^[a-zA-Z ]*$/.test(v) || 'Numbers are not Allowed',
         NumeicOnly: v => /^\(?([0-9]{4})\)?[-. ]?([0-9]{7})$/.test(v) || 'Numbers are not Allowed',
         min: v => v.length >=3 || 'Minimum 3 Charaters Required',
         //min1: v => v.length >=3 || 'Minimum 3 Charaters Required',
         max: v => v.length <=15 || 'Maximum 15 Charaters Allowed',
         validEmail:v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
         },
    headers: [
      { text: "#",align: "start",sortable: false,value: "id"},
      { text: "Name", value: "name" },
      { text: "Email",sortable: false, value: "email" },
      { text: "Role", value: "role" },
      { text: "Photo",sortable: false, value: "photo" },
      { text: "Phone No",sortable: false, value: "phone_no" },
      { text: "Created At", sortable: false,value: "created_at" },
      { text: "Updated At",sortable: false, value: "updated_at" },
      { text: "Actions", sortable: false,value: "actions", sortable: false }
    ],
    users: [],
    editedIndex: -1,
    editedItem: {
        id: '',
      name: '',
      email:'',
      role:'',
      phone_no:'',
      photo: null,
      password:'',
      rpassword:'',
      created_at: '',
      updated_at: ''

    },
    defaultItem: {
        id: '',
      name: '',
      email:'',
      role:'',
      phone_no:'',
      password:'',
      photo:'',
      rpassword:'',
      created_at: '',
      updated_at: ''
    }
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New User" : "Edit User";
    },
    passwordMatch(){
        return this.editedItem.password != this.editedItem.rpassword ? 'Password Does Not Match!' : true;
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    }
  },

  created() {
    this.initialize();
  },

  methods: {
      uploadPhoto(item){
        if (this.editedItem.photo != null) {
          const index =  this.users.data.indexOf(item);
          let formData = new FormData();
          formData.append('photo', this.editedItem.photo, this.editedItem.photo.name)
          formData.append('user', item.id)
          axios.post('/api/users/photo', formData)
           .then(res =>{
               this.user.data[index].photo = res.data.user.photo
               this.editedItem.photo= null;
               })
           .catch(err => console.log(err.response))
        }
      },
      updateRole(item){
          const index =this.users.data.indexOf(item);
          axios.post('/api/users/role', {'role': item.role  , 'user': item.id })
          .then(res => {
              this.snackbarText = res.data.user.name + "'s Role is Updated to " + res.data.user.role
              this.snackbar = true
          })
          .catch(error => {
              this.snackbarText = error.response.data.user.name + "'s Role Cannot be Updated to" + error.response.data.user.role
              this.users.data[index].role = error.response.data.user.role
              this.snackbar = true
              console.dir(error.response)
          })
      },
      checkEmail(){
        if (/.+@.+\..+/.test(this.editedItem.email)) {
            axios.post('/api/email/verify',{'email': this.editedItem.email})
            .then(res => {
                this.success = res.data.message;
                this.error ='';
            })
            .catch(err => {
                this.success = '';
                this.error = "Email Already Exists!";
            })
        }
    },
      selectAll(e){
          this.selected = [];

          if (e.length > 0){
              this.selected = e.map(val => val.id)
          }
          //console.dir(this.selected);
      },
      deleteAll(){
    let decide =  confirm("Are you sure you want to delete these users?");
      if (decide) {
           axios.post('/api/users/delete',{'users':this.selected})
           .then(res => {
            this.snackbarText = "Record Delete Successfully!";
            this.selected.map(val => {
                const index = this.users.data.indexOf(val);
                this.users.data.splice(index, 1);
             })
             this.snackbar =true;
            })
           .catch(err => {
            console.log(err.response)
            this.snackbarText = "Error Deleting Record!";
            this.snackbar =true;
            })
        }
      },
      search_it(e){
          if(e.length > 3){
              axios.get(`/api/users/${e}`)
              .then(res => {this.users = res.data.users})
              .catch(err => console.dir(err.response))
          }
          if (e.length <= 0){
              axios.get('/api/users')
              .then(res => this.users = res.data.users)
              .catch(err => console.dir(err.response))
          }
      },
      paginate(e){
          //console.dir(e)
        axios.get(`/api/users?page=${e.page}`, {params:{'per_page':e.itemsPerPage}})
        .then(res => {
            this.users = res.data.users
            this.roles = res.data.roles
            console.dir(res.data.users);
            console.dir(res.data.roles);
            })
        .catch(err => {
            if(err.response.status == 401){
                localStorage.removeItem('token');
                this.$router.push('/login');
            }
        })
      },
    initialize() {
       // Add a request interceptor
      axios.interceptors.request.use((config) =>{
          // Do something before request is sent
          this.loading = true;
          return config;
        },
        (error) => {
          // Do something with request error
          this.loading = false;
          return Promise.reject(error);
        }
      );

      // Add a response interceptor
      axios.interceptors.response.use(
        (response) => {
          // Any status code that lie within the range of 2xx cause this function to trigger
          // Do something with response data
          this.loading = false;
          return response;
        },
        (error) =>{
          // Any status codes that falls outside the range of 2xx cause this function to trigger
          // Do something with response error
          this.loading = false;
          return Promise.reject(error);
        });


    },

    editItem(item) {
      this.editedIndex = this.users.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      const index = this.users.data.indexOf(item);
      let decide =  confirm("Are you sure you want to delete this item?");
      if (decide) {
           axios.delete('/api/users/'+item.id)
           .then(res => {
            this.snackbarText = "Record Delete Successfully!";
            this.snackbar =true;
            this.users.data.splice(index, 1);
            })
           .catch(err => {
            console.log(err.response)
            this.snackbarText = "Error Deleting Record!";
            this.snackbar =true;
            })
      }
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {

      if (this.editedIndex > -1) {
          const index =this.editedIndex;
        axios.put('/api/users/'+this.editedItem.id ,this.editedItem)
        .then(res => {
            this.snackbarText = "Record Updated Successfully!";
            this.snackbar =true;
            Object.assign(this.users.data[index], res.data.user)})
        .catch(err => {
            console.log(err.response)
            this.snackbarText = "Error Updating Record!";
            this.snackbar =true;
            })
      } else {
        axios.post('/api/users',this.editedItem)
        .then(res => {
            this.snackbarText = "Record Added Successfully!";
            this.snackbar =true;
            this.users.data.push(res.data.user)
            })
        .catch(err => {
            console.log(err.response)
            this.snackbarText = "Error Inserting Record!";
            this.snackbar =true;
            })
      }
      this.close();
    }
  }
};
</script>

<style scoped>
</style>
