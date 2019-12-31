import React, { useEffect, useState } from 'react'
import Task from './Task'
import ScrollReveal from 'scrollreveal'
let TaskList = ({ tasks }) => {


    const [totalTasks] = useState(tasks.length)
    // const [newYear, setNewYear] = useState()

    let newYear = null
    useEffect(() => {
        const container = document.querySelector('.tasks');
        const sr = ScrollReveal({ container: container });
        sr.reveal('.task', {
            origin: 'bottom',
            duration: 500,
            delay: 150,
            distance: '100px',
            scale: 1,
            easing: 'ease',
        });

        sr.reveal('.titles__year', {
            origin: 'bottom',
            duration: 500,
            delay: 150,
            distance: '100px',
            scale: 1,
            easing: 'ease',
        });
    })

    return (
        <div className="tasks">
            <div className="titles">
                <div className="titles__id">Datum</div>
                <div className="titles__company">Bedrijf</div>
                <div className="titles__hours">Uren</div>
            </div>

            {totalTasks <= 0 &&
                <p>Geen Prestatie</p>
            }


            {tasks.map((task, i) => {

                let date = new Date(task.date)
                // let nextTaskDate = new Date(task[i + 1].date)
                if (i === 0) {
                    newYear = date.getFullYear()
                    return (
                        <div key={task.id} className="">
                            <div className="titles__year">{date.getFullYear()}</div>
                            <Task key={task.id} task={task} />
                        </div>)
                }
                if (date.getFullYear() !== newYear) {
                    newYear = date.getFullYear()
                    return (
                        <div key={task.id} className="">
                            <div className="titles__year">{date.getFullYear()}</div>
                            <Task key={task.id} task={task} />
                        </div>
                    )
                } else {
                    return (
                        <Task key={task.id} task={task} />
                    )
                }
            })
            }
        </div>
    )
}

export default TaskList