<template>
  <v-app id="inspire">
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">

            <v-card class="elevation-12">
              <v-toolbar color="error" dark flat>
                <v-toolbar-title>SignUp</v-toolbar-title>
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
                  label="UserName"
                  v-model="name"
                  :counter="15"
                  :rules="[rules.required , rules.alphaNumeicOnly ,rules.min , rules.max]"
                  required
                  name="name"
                  prepend-icon="mdi-account-box"
                  />

                 <v-text-field color="error"
                  label="Phone No"
                  v-model="phone_no"
                  :rules="[rules.required , rules.NumeicOnly ]"
                  required
                  name="phone_no"
                  prepend-icon="mdi-phone"
                  />

                  <v-text-field color="error"
                  label="Email"
                  v-model="email"
                  :rules="[rules.required ,rules.validEmail]"
                  required
                  name="email"
                  prepend-icon="mdi-email"
                  type="email" />

                  <v-text-field color="error"
                    label="Password"
                    name="password"
                    v-model="password"
                    :counter="15"
                    :rules="[rules.required ,rules.validPassword , rules.max , rules.password]"
                    :append-icon="value ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="() => (value = !value)"
                    :type="value ? 'password' : 'text'"
                    prepend-icon="mdi-lock"
                    required
                  />
                  <v-text-field color="error"
                    label="Retype Password"
                    name="rpassword"
                    v-model="rpassword"
                    :counter="15"
                    :rules="[rules.required ,rules.validPassword , rules.max , rules.password]"
                    prepend-icon="mdi-lock"
                    :append-icon="value ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="() => (value = !value)"
                    :type="value ? 'password' : 'text'"
                    required
                  />
                </v-form>
            <v-row align="center" style="max-width: 650px">
                <v-col >
                    <v-divider  class="mx-2"></v-divider>
                </v-col>
            </v-row >
            <div>
            <v-btn  to="/login">Login</v-btn>
            </div>
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn color="error" block :disabled="!valid" @click="signup">Submit</v-btn>
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
      name:'',
      rules:{
         required: v => !!v || 'This Feild is Required',
         alphaNumeicOnly: v => /^[a-zA-Z ]*$/.test(v) || 'Numbers are not Allowed',
         NumeicOnly: v => /^\(?([0-9]{4})\)?[-. ]?([0-9]{7})$/.test(v) || 'Characters are not Allowed',
         min: v => v.length >=3 || 'Minimum 3 Charaters Required',
         validPassword: v => (v || '').indexOf(' ') < 0 || 'No spaces are allowed',
         max: v => v.length <=15 || 'Maximum 15 Charaters Allowed',
         validEmail: v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
        password: value => {
          const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
          return (
            pattern.test(value) ||
            "Min. 8 characters with at least one capital letter, a number and a special character."
          );
        },

         },
         value:true,
      phone_no:'',
      email: '',
      password: '',
      rpassword:'',
      loading: false,
      snackbar: false,
      text:'',
    }
  },
  created() {
    this.$vuetify.theme.dark = true
  },
  methods:

  {
    signup: function() {
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

      axios.post('/api/signup',{'name':this.name,'phone_no':this.phone_no,'email':this.email ,'password':this.password})
      .then( res => {
          //console.dir(res);
           localStorage.setItem('loggedIn', true)
           this.$router.push('/login').then(res => console.log('SignUp Successfully')).catch(err => console.log(err.response))
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

    .label_style{
        text-align: center;
        color: white !important;
    }
</style>
