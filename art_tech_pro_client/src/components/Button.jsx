import React from 'react'



let Button = ({ name, action, type }) => {

    return (
        <div onClick={action} className={type === "main" ? "button button__main" : "button button__second"}>
            <div className="button__text">{name}</div>
        </div>
    )

}


export default Button