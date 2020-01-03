import React, { useEffect, useState } from 'react'
import BaseLayout from '../layouts/base';
import SelectOptions from '../components/SelectOptions'
import { checkJWTValid, getEmailFromJWT } from '../utils/jwt'
import Button from '../components/Button'
import { getMonthHours, getMonthIncome, getMonthAndYear } from '../utils/helper'
import { logout } from '../utils/api'
import { Redirect } from 'react-router-dom'
import { withRouter } from "react-router-dom";
import DatePicker from "react-datepicker"
import "react-datepicker/dist/react-datepicker.css"

import axios from 'axios'



let ReportPage = () => {
    const [isAuth] = useState(checkJWTValid())
    const [loading, setLoading] = useState(true)
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))
    const [error, setError] = useState()

    const [isResult, setIsResult] = useState(true)

    const [clients, setClients] = useState()
    const [clientSelected, setSelectedOption] = useState({ value: 0, label: "Alle bedrijven" })


    const [totalIncome, setTotalIncome] = useState(0)
    const [totalHours, setTotalHours] = useState(0)
    const [startDate, setStartDate] = useState(0)

    const [isDataLoading, setIsDataLoading] = useState(false)

    let handleClientChange = clientSelected => {
        setSelectedOption(clientSelected)
        console.log(clientSelected.value)


        //calculate per company
    }
    let handleDateChange = date => {
        setStartDate(date)

        //calculate per month
    }

    let handleSubmit = () => {

        setIsDataLoading(true)
        //if date and client changed
        if (startDate === 0 && clientSelected.value === 0) {
            getMonthHours("ALL").then((value) => {
                setTotalHours(value)

            }).then(() => {
                getMonthIncome("ALL").then((value) => {
                    setTotalIncome(value)
                    setIsDataLoading(false)
                })
            })

        } else {
            let date = getMonthAndYear(startDate)

            if (startDate !== 0) {
                getMonthHours(date.month, date.year, clientSelected.value).then(value => {
                    setTotalHours(value)
                }).then(() => {
                    getMonthIncome(date.month, date.year, clientSelected.value).then(value => {
                        setTotalIncome(value)
                        setIsDataLoading(false)
                    })
                })
            } else {
                getMonthHours(0, 0, clientSelected.value).then(value => {
                    setTotalHours(value)
                }).then(() => {
                    getMonthIncome(0, 0, clientSelected.value).then(value => {

                        setTotalIncome(value)
                        setIsDataLoading(false)
                    })
                })
            }
        }
    }

    useEffect(() => {
        let getClients = async () => {
            await axios.post(process.env.REACT_APP_API_URL + "/api/getclientsbyuser", { 'email': getEmailFromJWT() }
                , { headers: { "Authorization": `Bearer ${token}` } })
                .then((response) => {
                    if (response.status === 200) {
                        console.log(response.data)
                        let allClients = response.data
                        allClients.unshift({ value: 0, label: "Alle bedrijven" })
                        setClients(allClients)

                    }
                }).catch((error) => {
                    setError("Fout! doe opnieuw")
                    console.log(error)
                })
        }

        if (loading) {
            getClients()
                .then(() => {
                    getMonthHours("ALL").then((value) => {
                        setTotalHours(value)

                    }).then(() => {
                        getMonthIncome("ALL").then((value) => {
                            setTotalIncome(value)
                            setLoading(false)
                        })
                    })
                })

        }
        console.log(totalHours)
    }, [loading, token, totalHours])


    if (isAuth) {
        if (!loading) {
            return (
                <div className="reports">
                    <label htmlFor="aaa" >Selecteer bedrijf</label>
                    <SelectOptions placeholder="Alle bedrijven" options={clients} onChange={(event) => handleClientChange(event)} value={clientSelected} />
                    <label htmlFor="aaa" >Selecteer maand</label>
                    <DatePicker
                        selected={startDate}
                        onChange={date => handleDateChange(date)}
                        dateFormat="MM/yyyy"
                        showMonthYearPicker
                        placeholderText="Alle tijden"
                    />
                    <Button name="MAGIC" type="main" action={() => handleSubmit()} />

                    {isResult ? (
                        <div>
                            <hr />
                            <div className="title__main">Resultaat</div>
                            <div className="title__second">Indienst uren </div>

                            {isDataLoading ? (
                                <div className="text">
                                    <div className="loading__text">
                                        <img src="./svgs/loading_anim.svg" alt="Loading ..." />
                                    </div>
                                    Uren </div>

                            ) : (<div className="text">
                                {totalHours} Uren </div>)}

                            <div className="title__second">Inkomst </div>
                            {isDataLoading ? (

                                <div className="text"><div className="loading__text">
                                    <img src="./svgs/loading_anim.svg" alt="Loading ..." />
                                </div> EUR </div>

                            ) : (<div className="text">{(Math.round(totalIncome * 100) / 100).toFixed(2)} EUR </div>)}

                        </div>) : (
                            <div>
                                <hr />
                                <div className="title__main">Resultaat</div>
                                <div className="text">Kies bedrijf en maand bovenaan. </div>
                            </div>


                        )
                    }
                </div >
            )
        } else {
            return (
                <div className="loading">
                    <img src="../svgs/loading_anim.svg" alt="Loading ..." />
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

export default withRouter(BaseLayout(ReportPage))