import React from 'react';
import { FiList, FiFolder, FiPlus, FiHome } from 'react-icons/fi';
import { Redirect } from 'react-router-dom'
import { useLocation } from "react-router";

let Nav = () => {

    console.log(useLocation().pathname)

    const isHome = (useLocation().pathname === "/") ? true : false
    return (
        <div className="nav">

            {isHome ? (
                <a href="/task/new" className="add__task">
                    <FiPlus size="2em" />
                </a>
            ) : (
                    <a href="/" className="to__home">
                        <FiHome size="1.7em" />
                    </a>
                )}


            <a href="/tasks">
                <li onClick={() => <Redirect to="/tasks" />} className={(useLocation().pathname === "/tasks") ? "nav__item active" : "nav__item"}><FiList size="2em" />
                    <div className="nav_item--label">
                        Prestatie</div>
                </li>
            </a>

            <a href="/reports">
                <li onClick={() => console.log('clicked')} className={(useLocation().pathname === "/reports") ? "nav__item active" : "nav__item"}><FiFolder size="2em" />
                    <div className="nav_item--label">
                        Rapport</div>
                </li>
            </a>

        </div>
    )
}

export default Nav