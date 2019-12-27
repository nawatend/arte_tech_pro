import React from 'react';


let TextField = ({ type, id, label, defaultValue, onChange }) => {

    return (
        <div className="textfield__main">
            <label htmlFor="email" >{label}</label>
            <input
                onChange={onChange}
                id={id}
                type={type}
                defaultValue={defaultValue}
                
            />
        </div>
    )
}

export default TextField