import * as jwt from 'jwt-decode';

//check if jwt is not expired
let checkJWTValid = () => {

    let isJWTValid = false
    const token = localStorage.getItem('ATP_token')

    if (token !== 'undefined' && token !== null) {
        let decodedToken = jwt(token, {
            complete: true
        })
        let dateNow = new Date()

        //console.log(decodedToken)
        // divided by 1000 cuz getTime() is in milisecond and .exp is in seconds
        if (decodedToken.exp > dateNow.getTime() / 1000) {
            isJWTValid = true

        } else {
            isJWTValid = false
        }
    }
    return isJWTValid
}

//like name says
let getEmailFromJWT = () => {

    const token = localStorage.getItem('ATP_token')

    let email = ""
    if (token !== 'undefined' && token !== null) {
        let decodedToken = jwt(token, {
            complete: true
        })
        email = decodedToken.username
    }
    return email
}

let getRole = (token = localStorage.getItem('ATP_token')) => {

    let userType = ""
    if (token !== 'undefined' && token !== null) {

        jwt(token, {
            complete: true
        }).roles.forEach(role => {

            if (role === "ROLE_FREELANCER") {
                userType = role
            }

            if (role === "ROLE_EMPLOYEE") {
                userType = role
            }

            if (role === "ROLE_ADMIN") {
                userType = role
            }
            if (role === "ROLE_CLIENT") {
                userType = role
            }
        });
    }
    //console.log(userType)
    return userType
}


let getRoleByToken = (token) => {

    let userType = ""
    if (token !== 'undefined' && token !== null) {

        jwt(token, {
            complete: true
        }).roles.forEach(role => {

            if (role === "ROLE_FREELANCER") {
                userType = role
            }

            if (role === "ROLE_EMPLOYEE") {
                userType = role
            }
        });
    }
    return userType
}

export {
    checkJWTValid,
    getEmailFromJWT,
    getRole,
    getRoleByToken
}