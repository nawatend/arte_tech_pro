import React, { useEffect, useState } from 'react'
import BaseLayout from '../layouts/base';
import Button from '../components/Button'
import { FiList, FiFolder, FiPlus, FiEdit } from 'react-icons/fi';
import { checkJWTValid, getRole } from '../utils/jwt'
import { logout } from '../utils/api'
import { Redirect } from 'react-router-dom'
import { withRouter } from "react-router-dom";
import { getMonthIncome, getMonthHours, getRate } from '../utils/helper'


let HomePage = () => {
    const [isAuth] = useState(checkJWTValid())
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))
    const [userId, setUserId] = useState(localStorage.getItem('ATP_userId'))
    const [monthIncome, setMonthIncome] = useState()
    const [monthHours, setMonthHours] = useState()
    const [userType, setUserType] = useState(getRole())
    const [loading, setLoading] = useState(true)
    const [rate, setRate] = useState({
        hourRate: 0,
        TransportCost: 0
    })

    useEffect(() => {
        setToken(localStorage.getItem('ATP_token'))


        if (loading) {
            getMonthIncome().then((value) => {
                setMonthIncome(value)
            }).then(() => {
                getMonthHours().then((value) => {
                    setMonthHours(value)
                })
            }).then(() => {
                getRate().then(rate => {
                    setRate(rate)
                    setLoading(false)
                })
            })
        }

    }, [loading, monthHours, monthIncome, rate])

    if (isAuth) {

        //freelancer or employee
        if (!loading) {
            if (userType === "ROLE_FREELANCER") {
                return (
                    <div>
                        <div className="title__main">Uren gewerkt in deze maand</div>
                        <div className="text">{monthHours} Uren </div>
                        <div className="title__main">Totaal verdiende deze maand</div>
                        <div className="text">{(Math.round(monthIncome * 100) / 100).toFixed(2)} EUR </div>
                        <hr />


                        {/* only for freelancer */}
                        <div className="rate">
                            <div className="rate__hour">
                                <div className="title__main">Uur tarief</div>
                                <div className="text">{(Math.round(rate.hourRate * 100) / 100).toFixed(2)} EUR </div>
                            </div>

                            <div className="rate__transport">
                                <div className="title__main">Transportkost</div>
                                <div className="text">{(Math.round(rate.TransportCost * 100) / 100).toFixed(2)} EUR </div>
                            </div>
                            <a href="/editrate" className="rate__edit">
                                <FiEdit size="1.7em" />
                            </a>
                        </div>
                        <hr />
                        {/* end of only for freelancer */}


                    </div>
                )
            } else {
                return (
                    <div>
                        <div className="title__main">Uren gewerkt in deze maand</div>
                        <div className="text">{monthHours} Uren </div>
                        <div className="title__main">Totaal verdiende deze maand</div>
                        <div className="text">{(Math.round(monthIncome * 100) / 100).toFixed(2)} EUR </div>
                        <hr />

                    </div>
                )
            }
        } else {
            return (
                <div className="loading">
                    <img src="./svgs/loading_anim.svg" alt="Loading ..." />
                </div>
            )
        }
    }
    else {
        logout()
        return (
            <Redirect to='/login' />
        )
    }

}

export default withRouter(BaseLayout(HomePage))