import React from 'react'
import { BrowserRouter as Router, Route, Switch } from "react-router-dom";

import HomePage from '../pages/index'
import TasksPage from '../pages/tasks'
import ReportPage from '../pages/report'
import AddTaskPage from '../pages/addTask'
import LoginPage from '../pages/login'
import EditRatePage from '../pages/editRate'
let ATPRouter = () => {


    return (
        <Router>
            <Switch>
                <Route exact path="/">
                    <HomePage />
                </Route>
                <Route exact path="/reports">
                    <ReportPage />
                </Route>

                <Route exact path="/tasks">
                    <TasksPage />
                </Route>

                <Route exact path="/task/new">
                    <AddTaskPage />
                </Route>

                <Route exact path="/login">
                    <LoginPage />
                </Route>


                <Route exact path="/editrate">
                    <EditRatePage />
                </Route>
            </Switch>
        </Router>
    );

}


export default ATPRouter