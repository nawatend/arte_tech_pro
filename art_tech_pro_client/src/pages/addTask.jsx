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

let AddTaskPage = () => {

    const [loading, setLoading] = useState(true)
    const [taskSaved, setTaskSaved] = useState(false)
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))
    const [workerId] = useState(localStorage.getItem('ATP_userId'))
    //newTask data
    const [description, setDescription] = useState("")
    const [used, setUsed] = useState("")
    const [startTime, setStartTime] = useState()
    const [endTime, setEndTime] = useState()
    const [pauzeMinutes, setPauzeMinutes] = useState(0)
    const [clientId, setClientId] = useState(0)
    const [startDate, setStartDate] = useState()
    const [km, setKm] = useState(0)
    const [error, setError] = useState()
    const [isAuth] = useState(checkJWTValid())


    const [clients, setClients] = useState()
    const [clientSelected, setSelectedOption] = useState()

    let handleClientChange = clientSelected => {
        setSelectedOption(clientSelected)
        setClientId(clientSelected.value)
        console.log(clientSelected.value)
    }


    let handleSubmit = async () => {
        //post new task

        let newTask = {
            clientId: clientId,
            workerId: workerId,
            date: startDate,
            startTime: startTime,
            endTime: endTime,
            description: description,
            used: used,
            pauzeMinutes: pauzeMinutes,
            km: km
        }


        await axios.post(process.env.REACT_APP_API_URL + "/api/savetask", newTask
            , { headers: { "Authorization": `Bearer ${token}` } })
            .then((response) => {
                if (response.status === 200) {
                    console.log(response.data)
                    NotificationManager.success('Succes', 'Prestatie Toegevoegd', 2300)
                    setTimeout(() => { setTaskSaved(true) }, 2400);
                }

            }).catch((error) => {
                setError("Fout! doe opnieuw")
                console.log(error)
            })
    }

    useEffect(() => {
        setToken(localStorage.getItem('ATP_token'))

        //get all clients for selection
        let getClients = async () => {
            await axios.post(process.env.REACT_APP_API_URL + "/api/clients", {}
                , { headers: { "Authorization": `Bearer ${token}` } })
                .then((response) => {
                    if (response.status === 200) {
                        console.log(response.data)
                        setClients(response.data)
                        setLoading(false)
                    }

                }).catch((error) => {
                    setError("Fout! doe opnieuw")
                    console.log(error)
                })
        }
        getClients()

    }, [token])

    if (isAuth) {
        if (taskSaved) {
            return (
                <Redirect to='/' />
            )
        } else if (!loading) {
            return (
                <div className="new__task">
                    <NotificationContainer />
                    <div className="title__main">Nieuwe Prestatie</div>
                    <SelectOptions placeholder="Kies een bedrijf" options={clients} onChange={(event) => handleClientChange(event)} value={clientSelected} />


                    <DatePicker todayButton="Vandaag" placeholderText="Kies een datum" dateFormat="dd/MM/yyyy" selected={startDate} onChange={date => setStartDate(date.getTime())} />
                    <label htmlFor="aaa" >Start tijd</label>
                    <DatePicker
                        placeholderText="Kies start tijd"
                        selected={startTime}
                        onChange={startTime => setStartTime(startTime.getTime())}
                        showTimeSelect
                        showTimeSelectOnly
                        timeIntervals={15}
                        timeCaption="Time"
                        dateFormat="h:mm aa"
                    />
                    <label htmlFor="aaa" >Eind Time</label>
                    <DatePicker
                        placeholderText="Kies eind tijd"
                        selected={endTime}
                        onChange={endTime => setEndTime(endTime.getTime())}
                        showTimeSelect
                        showTimeSelectOnly
                        timeIntervals={15}
                        timeCaption="Time"
                        dateFormat="h:mm aa"
                    />

                    <TextField placeholder="Tussen 0 - 60" type="number" label="Pauze in minuten" onChange={(event) => { setPauzeMinutes(event.target.value) }} />
                    <TextField placeholder="Aantal km" type="number" label="Aantal transport km" onChange={(event) => { setKm(event.target.value) }} />
                    <TextAreaField label="Beschrijving" onChange={(event) => { setDescription(event.target.value) }} />
                    <TextField placeholder="Bv. UTP kabel, brain" type="text" label="Gebruikte Materialen" onChange={(event) => { setUsed(event.target.value) }} />
                    <Button name="STUUR" type="main" action={() => handleSubmit()} />
                    <p className="error">{error}</p>
                </div>
            )
        } else {
            return (
                <div className="loading">
                    <img src="../svgs/loading_anim.svg" alt="Loading ..." />
                </div>
            )
        }
    } else {
        logout()
        return (
            <Redirect to='/login' />
        )
    }
}

export default withRouter(BaseLayout(AddTaskPage))