import React from 'react'

let Task = ({ task }) => {
    return (
        <div className="task">
            <div className="task__id">24/12</div>
            <div className="task__company">{task.client.companyName}</div>
            <div className="task__hours">35</div>
        </div>
    )
}

export default Task