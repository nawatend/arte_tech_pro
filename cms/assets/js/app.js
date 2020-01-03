/*
 * Welcome to your app's main JavaScript file!
 */

//import css
require('../css/main.sass');

//toggle password to text
let togglePasswordToString = (id)=> {
   let x = document.getElementById(id);
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}
//check for password match
let form_password = document.getElementById("form_password")
let checkBoxShowPassword = document.getElementById('show_password')
let errorPasswordMatch = document.getElementById("error_password_match")

if(form_password){
checkBoxShowPassword.addEventListener("click", ()=>{

    togglePasswordToString("form_password")
    console.log('changed')
})


}