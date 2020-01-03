const setUser = (userObj) => {
    return {
        type: "SET_USER",
        payload: userObj
    }
}
//not in use
const logout = (userObj) => {
    return {
        type: "LOG_OUT",

    }
}

export default {
    setUser,
    logout
}