<template>
  <v-app id="inspire">
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">

            <v-card class="elevation-12">
              <v-toolbar color="error" dark flat>
                <v-toolbar-title>Login</v-toolbar-title>
                <v-spacer />
              </v-toolbar>
              <v-card-text>
                <v-progress-linear
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  top
                  color="white accent-4"
                ></v-progress-linear>

                <v-form
                    ref="from"
                    v-model="valid"

                >
                  <v-text-field color="error"
                  label="Email"
                  v-model="email"
                  :rules="emailRules"
                  required
                  name="login"
                  prepend-icon="mdi-account"
                  type="email" />

                  <v-text-field color="error"
                    id="password"
                    label="Password"
                    name="password"
                    v-model="password"
                    :rules="passwordRules"
                    prepend-icon="mdi-lock"
                    type="password"
                    required
                  />
                </v-form>
            <v-row align="center" style="max-width: 650px">
                <v-col >
                    <v-divider  class="mx-2"></v-divider>
                </v-col>
            </v-row >
            <div>
            <v-btn  to="/signup">SignUp</v-btn>
            </div>
            <v-label class="mx-4 " ></v-label>
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn color="error" block :disabled="!valid" @click="login">Login</v-btn>
              </v-card-actions>
            </v-card>
            <v-snackbar
                  v-model="snackbar">
                  {{ text }}
                <v-btn
                 color="pink"
                 text
                 @click="snackbar = false">Close</v-btn>
             </v-snackbar>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>
<script>
export default {
  data() {
    return {
        valid: true,
      email: '',
      emailRules: [
        v => !!v || 'E-mail is required',
        v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
      ],
      password: '',
      passwordRules:[
          v => !!v || 'Password is required',
          v => (v && v.length >= 6) || 'Password must be greater than 6 characters',
      ],
      loading: false,
      snackbar: false,
      text:'',
    }
  },
  created() {
    this.$vuetify.theme.dark = true
  },
  methods: {
    login: function() {
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

      axios.post('/api/login',{'email':this.email ,'password':this.password})
      .then( res => {
          //console.dir(res);
           localStorage.setItem("token",res.data.token)
           localStorage.setItem('loggedIn', true)
           if(res.data.isAdmin){
             this.$router.push('/admin')
            .then(res => console.log('Login Successfully'))
            .catch(err => console.log(err))
           }else{
               this.text = "You Need to be LooggedIn as Admin";
               this.snackbar =true;
           }

      })
      .catch( err => {
          this.text = err.response.data.status
          this.snackbar = true;
          })
    }
  }
};
</script>

<style scoped>
</style>
