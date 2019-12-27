import React from 'react';
import { FiList, FiFolder, FiPlus } from 'react-icons/fi';
import { Redirect } from 'react-router-dom'


let Nav = () => {
    return (
        <div className="nav">
            <a href="/" className="add__task">
                <FiPlus size="2em" />
            </a>

            <a href="/tasks">
                <li onClick={() => <Redirect to="/tasks" />} className="nav__item"><FiList size="2em" />
                    <div className="nav_item--label">
                        Prestatie</div>
                </li>
            </a>

            <a href="/reports">
                <li onClick={() => console.log('clicked')} className="nav__item"><FiFolder size="2em" />
                    <div className="nav_item--label">
                        Rapport</div>
                </li>
            </a>

        </div>
    )
}

export default Nav