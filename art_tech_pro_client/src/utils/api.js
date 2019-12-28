import axios from 'axios'



let login = async (email, password) => {

    let cred = {
        "username": email,
        "password": password
    }
    console.log(email + '---' + password)


    await axios.post(process.env.REACT_APP_API_URL + "/api/login_check", cred)
        .then(response => {

            if (response.status === 200) {
                localStorage.setItem("ATP_token", response.data)
                return true
            }
        })

}

let getTasksByUser = async () => {

    let tasks = {}

    await axios.get(process.env.REACT_APP_API_URL + "getsomething")
        .then(response => {
            tasks = response
        })
    return tasks

}

let logout = () => {

    localStorage.removeItem("ATP_token")
    localStorage.removeItem("ATP_userId")
}

export {
    getTasksByUser,
    login,
    logout
}