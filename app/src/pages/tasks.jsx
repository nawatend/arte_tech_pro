import React, { useState, useEffect } from 'react';
import axios from 'axios'
import { getEmailFromJWT } from '../utils/jwt'
import BaseLayout from '../layouts/base';

import { checkJWTValid } from '../utils/jwt'
import { logout } from '../utils/api'
import { Redirect } from 'react-router-dom'


import TaskList from '../components/TaskList';
let TasksPage = () => {

    const [isAuth] = useState(checkJWTValid())
    //tasks are ordered by Date -> DESC
    const [tasks, setTasks] = useState()
    const [error, setError] = useState()
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))
    const [email, setEmail] = useState(getEmailFromJWT())
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        setToken(localStorage.getItem('ATP_token'))

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
        getTasks()

        setEmail(getEmailFromJWT())
    }, [email, token])

    if (isAuth) {
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
    else {
        logout()
        return (
            <Redirect to='/login' />
        )
    }
}

export default BaseLayout(TasksPage)