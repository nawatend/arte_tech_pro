
import * as jwt from 'jwt-decode';

let checkJWTValid = () => {

    let isJWTValid = false

    const token = localStorage.getItem('ATP_token')

    if (token !== 'undefined' && token !== null) {
        let decodedToken = jwt(token, { complete: true })
        let dateNow = new Date()

        if (decodedToken.exp < dateNow.getTime()) {

            isJWTValid = true

        } else {
            isJWTValid = false
        }
    }
    return isJWTValid
}

let getEmailFromJWT = () => {

    const token = localStorage.getItem('ATP_token')

    let email = ""
    if (token !== 'undefined' && token !== null) {
        let decodedToken = jwt(token, { complete: true })
        email = decodedToken.username
    }
    return email
}

let getRole = () => {
    const token = localStorage.getItem('ATP_token')

    let userType = ""
    if (token !== 'undefined' && token !== null) {

        jwt(token, { complete: true }).roles.forEach(role => {

            if (role === "ROLE_FREELANCER") {
                userType = role
            }

            if (role === "ROLE_EMPLOYEE") {
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

        jwt(token, { complete: true }).roles.forEach(role => {

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

export { checkJWTValid, getEmailFromJWT, getRole, getRoleByToken }