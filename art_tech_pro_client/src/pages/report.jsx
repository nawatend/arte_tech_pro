import React, { useEffect, useState } from 'react'
import BaseLayout from '../layouts/base';

import { checkJWTValid } from '../utils/jwt'
import { logout } from '../utils/api'
import { Redirect } from 'react-router-dom'
import { withRouter } from "react-router-dom";

let ReportPage = () => {
    const [isAuth] = useState(checkJWTValid())
    const [token, setToken] = useState(localStorage.getItem('ATP_token'))

    useEffect(() => {
        setToken(localStorage.getItem('ATP_token'))
    }, [])
    if (isAuth) {
        return (

            <div>Reports Page</div>
        )
    }
    else {
        logout()
        return (
            <Redirect to='/login' />
        )
    }
}

export default withRouter(BaseLayout(ReportPage))