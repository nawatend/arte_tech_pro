import React from 'react';


let TextAreaField = ({ type, id, label, defaultValue, onChange }) => {

    return (
        <div className="textfield__main">
            <label htmlFor={label} >{label}</label>
            <textarea id={id}
                defaultValue={defaultValue} onChange={onChange} />
        </div>
    )
}

export default TextAreaField