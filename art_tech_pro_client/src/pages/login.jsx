
import AuthLayout from '../layouts/auth';
import React, { useEffect, useState } from 'react'
import ScrollReveal from 'scrollreveal'
import TextField from '../components/textFields/TextField'
import Button from '../components/Button'
import axios from 'axios'
import { Redirect } from 'react-router-dom'
import { withRouter } from "react-router-dom";
import { useDispatch } from 'react-redux'
import allActions from '../store/actions'
import { checkJWTValid } from '../utils/jwt'

let LoginPage = () => {

    const [email, setEmail] = useState()
    const [password, setPassword] = useState()
    const [isAuth, setIsAuth] = useState(false)
    const [error, setError] = useState()
    const [loading, setLoading] = useState(false)

    let handleAuth = async (email, password) => {

        setLoading(true)
        let cred = {
            "username": email,
            "password": password
        }
        console.log(email + '---' + password)


        await axios.post(process.env.REACT_APP_API_URL + "/api/login_check", cred)
            .then(response => {
                if (response.status === 200) {
                    localStorage.setItem("ATP_token", response.data.token)



                    console.log(response)
                    setIsAuth(true)
                    setLoading(false)
                }
                console.log(response)
            }).catch((error) => {
                setError("Fout bij email en wachtwoord")
                setLoading(false)
            })

    }


    useEffect(() => {
        const container = document.querySelector('.App');
        const sr = ScrollReveal({ container: container });
        sr.reveal('.auth__main__content', {
            origin: 'bottom',
            duration: 500,
            delay: 150,
            distance: '50px',
            scale: 1,
            easing: 'ease',
        });
        setIsAuth(checkJWTValid())
    }, [])


    if (!isAuth) {
        console.log("jwt is :" + checkJWTValid())
        if (loading) {
            return (<div className="loading">
                <img src="./svgs/loading_anim.svg" alt="Loading ..." />
            </div>)
        }
        return (
            <div>
                <TextField type="text" label="email" onChange={(event) => { setEmail(event.target.value) }} />
                <TextField type="password" label="wachtwoord" onChange={(event) => { setPassword(event.target.value) }} />
                <Button name="MELD AAN" type="main" action={() => handleAuth(email, password)} />
                <p className="error">{error}</p>
            </div>
        )
    }
    else {
        return (
            <Redirect to='/' />
        )
    }
}




export default withRouter(AuthLayout(LoginPage))