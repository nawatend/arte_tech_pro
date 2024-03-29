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
import { addDays, subDays, setMinutes, setHours } from '../utils/helper'

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
    const [pauzeMinutes, setPauzeMinutes] = useState()
    const [pauzeSelected, setPauzeSelected] = useState()


    const [startDate, setStartDate] = useState()
    const [km, setKm] = useState(0)
    const [error, setError] = useState()
    const [isAuth] = useState(checkJWTValid())


    const [clients, setClients] = useState()
    const [clientId, setClientId] = useState(0)
    const [clientSelected, setSelectedOption] = useState()


    let pauzeOptions = [
        { value: 0, label: '0 min' },
        { value: 15, label: '15 min' },
        { value: 30, label: '30 min' },
        { value: 45, label: '45 min' },
        { value: 60, label: '60 min' },
    ]


    let handleClientChange = clientSelected => {
        setSelectedOption(clientSelected)
        setClientId(clientSelected.value)
        console.log(clientSelected.value)
    }

    let handlePauzeChange = pauzeSelected => {
        setPauzeSelected(pauzeSelected)
        setPauzeMinutes(pauzeSelected.value)
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

                if (response.status === 400) {
                    console.log(response.data)

                }

            }).catch((error) => {
                if (error.response.status === 400) {
                    NotificationManager.error('Error', error.response.data.error, 2300)
                    setTimeout(() => { setError(error.response.data.error) }, 2400);
                }

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

        console.log(startDate)

    }, [startDate, token])

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
                    <p className="error">{error}</p>
                    <SelectOptions placeholder="Kies een bedrijf" options={clients} onChange={(event) => handleClientChange(event)} value={clientSelected} />

                    <DatePicker
                        todayButton="Vandaag"
                        placeholderText="Kies een datum"
                        dateFormat="dd/MM/yyyy"
                        selected={startDate}
                        minDate={subDays(new Date(), 15)}
                        maxDate={addDays(new Date(), 15)}
                        onChange={date => setStartDate(date.getTime())} />

                    <label htmlFor="aaa">Start tijd</label>
                    <DatePicker
                        placeholderText="Kies start tijd"
                        selected={startTime}
                        onChange={startTime => setStartTime(startTime.getTime())}
                        showTimeSelect
                        showTimeSelectOnly
                        timeIntervals={30}
                        timeCaption="Time"
                        dateFormat="h:mm aa"
                        minTime={setHours(setMinutes(new Date(0, 0, 0), 0), 8)}
                        maxTime={setHours(setMinutes(new Date(0, 0, 0), 0), 20)}

                    />
                    <label htmlFor="aaa" >Eind Time</label>
                    <DatePicker
                        placeholderText="Kies eind tijd"
                        selected={endTime}
                        onChange={endTime => setEndTime(endTime.getTime())}
                        showTimeSelect
                        showTimeSelectOnly
                        timeIntervals={30}
                        minTime={setHours(setMinutes(new Date(0, 0, 0), 0), 8)}
                        maxTime={setHours(setMinutes(new Date(0, 0, 0), 0), 20)}
                        timeCaption="Time"
                        dateFormat="h:mm aa"
                    />

                    <SelectOptions placeholder="Pauze Minuten" options={pauzeOptions} onChange={(event) => handlePauzeChange(event)} value={pauzeSelected} />


                    <TextField placeholder="Aantal km" type="number" label="Aantal transport km" onChange={(event) => { setKm(event.target.value) }} />
                    <TextAreaField label="Beschrijving" onChange={(event) => { setDescription(event.target.value) }} />
                    <TextField placeholder="Bv. UTP kabel, brain" type="text" label="Gebruikte Materialen" onChange={(event) => { setUsed(event.target.value) }} />
                    <Button name="STUUR" type="main" action={() => handleSubmit()} />

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