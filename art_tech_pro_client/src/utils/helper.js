import axios from 'axios'
import {
    getEmailFromJWT
} from './jwt'

let getThisMonth = () => {

    let d = new Date()
    let n = d.getMonth()

    return n
}

let getThisYear = () => {

    let d = new Date()
    let n = d.getFullYear()

    return n
}
//from online
let timeAddSub = (timeOne, timeTwo, flag = true) => { // flag=true to add values and flag=false to subtract values
    let tt1 = timeOne
    if (tt1 === '') {
        return ''
    }
    let t1 = tt1.split(':')
    let tt2 = timeTwo
    if (tt2 === '') {
        return ''
    }
    let t2 = tt2.split(':')
    tt1 = Number(t1[0]) * 60 + Number(t1[1])
    tt2 = Number(t2[0]) * 60 + Number(t2[1])
    let diff = 0
    if (flag) {
        diff = tt1 + tt2
    } else {
        diff = tt1 - tt2
    }
    t1[0] = Math.abs(Math.floor(parseInt(diff / 60))) // form hours
    t1[1] = Math.abs(diff % 60) // form minutes
    tt1 = ''
    if (diff < 0) {
        tt1 = '-'
    } // check for negative value
    return tt1 + t1.join(':')
}

let getMonthIncome = async () => {
    let monthTotal = 0

    const token = localStorage.getItem('ATP_token')
    const thisMonth = getThisMonth()
    const thisYear = getThisYear()

    await axios.post(process.env.REACT_APP_API_URL + "/api/tasksbyuser", {
            'email': getEmailFromJWT()
        }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        })
        .then(response => {
            if (response.status === 200 && response.data.lenght > 0) {


                response.data.forEach(task => {
                    let date = new Date(task.date)
                    // let nextTaskDate = new Date(task[i + 1].date)
                    if (thisMonth === date.getMonth() && thisYear === date.getFullYear()) {
                        monthTotal += task.totalCost
                    }
                })
            }
        }).catch((error) => {
            console.log(error)
        })
    return monthTotal

}

let getMonthHours = async () => {

    let monthTotal = "00:00"

    const token = localStorage.getItem('ATP_token')
    const thisMonth = getThisMonth()
    const thisYear = getThisYear()

    await axios.post(process.env.REACT_APP_API_URL + "/api/tasksbyuser", {
            'email': getEmailFromJWT()
        }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        })
        .then(response => {
            if (response.status === 200 && response.data.lenght > 0) {

                response.data.forEach(task => {
                    let date = new Date(task.date)
                    // let nextTaskDate = new Date(task[i + 1].date)
                    if (thisMonth === date.getMonth() && thisYear === date.getFullYear()) {
                        monthTotal = timeAddSub(task.TotalHours, monthTotal)
                    }
                })


            }
        }).catch((error) => {
            console.log(error)
        })

    //console.log(monthTotal)
    return monthTotal
}


let getRate = async () => {

    const token = localStorage.getItem('ATP_token')
    const userId = localStorage.getItem('ATP_userId')


    let rate = {}
    await axios.post(process.env.REACT_APP_API_URL + "/api/getrate", {
            userId: userId
        }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        })
        .then(response => {
            if (response.status === 200) {
                // console.log(response.data)
                rate = response.data
            }
        }).catch((error) => {
            console.log(error)
        })

    return rate
}

export {
    getMonthIncome,
    getMonthHours,
    getRate
}