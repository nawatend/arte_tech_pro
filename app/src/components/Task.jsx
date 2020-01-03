import React from 'react'

let Task = ({ task }) => {

    const date = new Date(task.date)
    const shortDate = date.getDate() + "/" + (date.getMonth() + 1)


    return (

        <div className="task">
            <div className="task__id">{shortDate}</div>
            <div className="task__company">{task.client.companyName}</div>
            <div className="task__hours">{task.TotalHours}</div>
        </div>
    )
}

export default Task