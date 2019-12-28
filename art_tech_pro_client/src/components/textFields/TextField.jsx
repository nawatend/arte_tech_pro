import React from 'react';


let TextField = ({ type, id, label, defaultValue, onChange, placeholder }) => {

    return (
        <div className="textfield__main">
            <label htmlFor={label} >{label}</label>
            <input
                onChange={onChange}
                id={id}
                type={type}
                defaultValue={defaultValue}
                placeholder={placeholder}

            />
        </div>
    )
}

export default TextField