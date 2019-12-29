import React from 'react'
import Select from 'react-select'


let SelectOptions = ({ options, onChange, value, placeholder }) => {





    return (
        <div className="">

            <Select
                value={value}
                onChange={onChange}
                options={options}
                placeholder={placeholder}
            />
        </div>
    )
}

export default SelectOptions