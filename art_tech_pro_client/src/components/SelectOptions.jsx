import React, { useState } from 'react'
import Select from 'react-select'


let SelectOptions = ({ options, onChange, value, placeholder }) => {





    return (
        <div className="">

            {/* <select>
                <option value="grapefruit">Grapefruit</option>
                <option value="lime">Lime</option>
                <option selected value="coconut">Coconut</option>
                <option value="mango">Mango</option>
            </select> */}

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