import React, { useEffect, useState } from 'react'
import BaseLayout from '../layouts/base';
import { TextField, TextAreaField } from '../components/textFields'
import SelectOptions from '../components/SelectOptions'
import Button from '../components/Button'
import { Redirect } from 'react-router-dom'
import { withRouter } from "react-router-dom";
import DatePicker from "react-datepicker"
import "react-datepicker/dist/react-datepicker.css"
import axios from 'axios'
import { NotificationContainer, NotificationManager } from 'react-notifications'
import 'react-notifications/lib/notifications.css'
import { checkJWTValid } from '../utils/jwt'
import { logout } from '../utils/api'
import { FiEdit } from 'react-icons/fi';
import { getRole } from '../utils/jwt'
import { getRate } from '../utils/helper'


let EditRatePage = () => {
    const [isAuth] = useState(checkJWTValid())
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))
    const [workerId] = useState(localStorage.getItem('ATP_userId'))
    const [hourRate, setHourRate] = useState(0)
    const [transportCost, setTransportCost] = useState(0)
    const [userType, setUserType] = useState(getRole())
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState()
    const [rateSaved, setRateSaved] = useState(false)

    const [oldRate, setOldRate] = useState({
        hourRate: 0,
        TransportCost: 0
    })
    let handleSubmit = async () => {

        await axios.post(process.env.REACT_APP_API_URL + "/api/saverate", { userId: workerId, hourRate: hourRate, transportCost: transportCost }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        })
            .then(response => {
                if (response.status === 200) {
                    console.log(response)
                    setRateSaved(true)
                }

            }).catch((error) => {
                setError("Fout: Vul juist in!")
                setLoading(false)
            })
    }


    useEffect(() => {
        setToken(localStorage.getItem('ATP_token'))

        //api call for update rate

        getRate().then(rate => {
            setOldRate(rate)
            setLoading(false)
        })

    }, [])


    if (isAuth && userType === "ROLE_FREELANCER") {
        if (rateSaved) {
            return (
                <Redirect to='/' />
            )
        }
        return (
            <div className="">
                <div className="rate">
                    <div className="rate__hour">
                        <div className="title__main">Oude uurtarief</div>
                        <div className="text">{(Math.round(oldRate.hourRate * 100) / 100).toFixed(2)}  EUR </div>
                    </div>

                    <div className="rate__transport">
                        <div className="title__main">Oude transportcost</div>
                        <div className="text">{(Math.round(oldRate.TransportCost * 100) / 100).toFixed(2)}  EUR </div>
                    </div>
                </div>
                <hr />
                <div className="form__edit">
                    <div className="title__main">Nieuwe tarief</div>
                    <TextField type="number" label="Uur tarief" onChange={(event) => { setHourRate(event.target.value) }} />
                    <TextField type="number" label="transportkost /km" onChange={(event) => { setTransportCost(event.target.value) }} />
                    <Button name="OPSLAAN" type="main" action={() => handleSubmit()} />
                    <p className="error">{error}</p>
                </div>
            </div>
        )
    }
    else {
        logout()
        return (
            <Redirect to='/login' />
        )
    }
}

export default withRouter(BaseLayout(EditRatePage))