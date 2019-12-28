import React, { useEffect, useState } from 'react'
import BaseLayout from '../layouts/base';
import { TextField, TextAreaField } from '../components/textFields'
import SelectOptions from '../components/SelectOptions'
import Button from '../components/Button'
import DatePicker from "react-datepicker"
import "react-datepicker/dist/react-datepicker.css"
import axios from 'axios'

let AddTaskPage = () => {

    const [loading, setLoading] = useState(false)
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))
    const [workerId, setWorkerId] = useState(localStorage.getItem('ATP_userId'))
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



    const options = [
        { value: '4', label: 'Chocolate' },
        { value: '4', label: 'Strawberry' },
        { value: '4', label: 'Vanilla' },
    ];
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
            .then(response => {
                if (response.status === 200) {
                    setLoading(false)
                    console.log(response.data)
                }

            }).catch((error) => {
                setError("Fout! doe opnieuw")
                console.log(error)
            })
    }

    useEffect(() => {
        setToken(localStorage.getItem('ATP_token'))

        console.log(startDate)
        console.log(startTime)
        console.log(endTime)


    }, [endTime, startDate, startTime])


    if (loading) {
        return (
            <div className="loading">
                <img src="../svgs/loading_anim.svg" alt="Loading ..." />
            </div>
        )
    } else {
        return (
            <div className="new__task">
                <div className="title__main">Nieuwe Prestatie</div>
                <SelectOptions placeholder="Kies een bedrijf" options={options} onChange={(event) => handleClientChange(event)} value={clientSelected} />


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
    }
}

export default BaseLayout(AddTaskPage)