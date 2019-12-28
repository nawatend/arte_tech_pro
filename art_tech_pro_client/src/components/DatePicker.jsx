import React, { useState } from "react";
import DatePicker from "react-datepicker";

import "react-datepicker/dist/react-datepicker.css";

// CSS Modules, react-datepicker-cssmodules.css
// import 'react-datepicker/dist/react-datepicker-cssmodules.css';

let MyDatePicker = () => {


    const [startDate, setStartDate] = useState(new Date())

    let handleChange = date => {
        this.setState({
            startDate: date
        });
    };


    return (
        <DatePicker
            selected={this.state.startDate}
            onChange={this.handleChange}
        />
    );

}

export default MyDatePicker
