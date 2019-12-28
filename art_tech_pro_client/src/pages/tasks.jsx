import React, { useState, useEffect } from 'react';
import axios from 'axios'
import { getEmailFromJWT } from '../utils/jwt'
import BaseLayout from '../layouts/base';

import TaskList from '../components/TaskList';
let TasksPage = () => {

    const [tasks, setTasks] = useState()
    const [error, setError] = useState()
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))
    const [email, setEmail] = useState(getEmailFromJWT())
    const [loading, setLoading] = useState(true)

    // eslint-disable-next-line react-hooks/exhaustive-deps
    let getTasks = async () => {
        await axios.post(process.env.REACT_APP_API_URL + "/api/tasksbyuser", { 'email': email }
            , { headers: { "Authorization": `Bearer ${token}` } })
            .then(response => {
                if (response.status === 200) {
                    setTasks(response.data)
                    setLoading(false)
                }

            }).catch((error) => {
                setError("Fout! Aanmeld opnieuw")
                console.log(error)
            })
    }

    useEffect(() => {
        setToken(localStorage.getItem('ATP_token'))
        if (!tasks) {
            getTasks()
        }

        setEmail(getEmailFromJWT())
    }, [getTasks, tasks, token])


    if (loading) {
        return (
            <div className="loading">
                <img src="./svgs/loading_anim.svg" alt="Loading ..." />
            </div>
        )
    } else {
        return (
            <div>
                <div className="title__main">Alle Prestaties</div>

                {tasks ? (
                    <TaskList tasks={tasks} />
                ) : (
                        "none"
                    )}

                <p>{error}</p>
            </div>
        )
    }

}

export default BaseLayout(TasksPage)