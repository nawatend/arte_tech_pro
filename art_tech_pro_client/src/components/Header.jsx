import React, { useEffect, useState } from 'react'
import { FiLogOut, FiHome } from 'react-icons/fi';
import { Redirect } from 'react-router-dom'
import { withRouter } from "react-router-dom";
import { useDispatch } from 'react-redux'
import { logout } from '../utils/api'

import axios from 'axios'
import allActions from '../store/actions'

import { getEmailFromJWT } from '../utils/jwt'

let Header = () => {


    const [isLogout, setIsLogout] = useState(false)
    const [token] = useState(localStorage.getItem('ATP_token'))
    const [user, setUser] = useState("____________")
    const dispatch = useDispatch()




    let exit = () => {
        logout()
        setIsLogout(true)
    }

    useEffect(() => {
        let setStates = async () => {
            await axios.post(process.env.REACT_APP_API_URL + "/api/getuserinfo", { 'email': getEmailFromJWT() },
                {
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                })
                .then(response => {
                    if (response.status === 200) {

                        // dispatch(allActions.userActions.setUser({ name: response.data.nickname }))
                        setUser(response.data.nickname)
                        localStorage.setItem("ATP_userId", response.data.id)
                    }

                }).catch((error) => {
                    console.log(error)
                })
        }
        setStates()

        dispatch(allActions.userActions.setUser({ name: user }))
    })


    if (isLogout) {
        return <Redirect to='/login' />
    } else {
        return (

            <div className="header">
                <div className="name">
                    Hi {user}!
                </div>
                <div onClick={() => exit()} className="logout">
                    <FiLogOut />
                </div>
            </div>
        )
    }
}


export default withRouter(Header)