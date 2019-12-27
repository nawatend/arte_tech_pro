import React, { useState } from 'react'
import { FiLogOut } from 'react-icons/fi';
import { Redirect } from 'react-router-dom'
import { withRouter } from "react-router-dom";
import { connect } from 'react-redux'
import { useSelector } from 'react-redux'
import { logout } from '../utils/api'

let Header = () => {

    const nickname = useSelector(state => state.nickname)
    const [isLogout, setIsLogout] = useState(false)

    let exit = () => {
        logout()
        setIsLogout(true)
    }

    if (isLogout) {
        return <Redirect to='/login' />
    } else {
        return (

            <div className="header">
                <div className="name">
                    Hi {nickname}!
                </div>
                <div onClick={() => exit()} className="logout">
                    <FiLogOut />
                </div>
            </div>
        )
    }
}

const mapStateToProps = (state) => ({
    nickname: state.nickname
})

export default withRouter(Header)