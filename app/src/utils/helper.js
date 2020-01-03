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


//mm/yyyy
let getMonthAndYear = (stringDate = "00/0000") => {


    let date = new Date(stringDate)

    let monthAndYear = {
        month: parseInt((date.getMonth() + 1)),
        year: parseInt(date.getFullYear())
    }
    console.log(monthAndYear)
    return monthAndYear
}

let getMonthIncome = async (month = -1, year = -1, clientId = -1) => {
    let monthTotal = 0

    const token = localStorage.getItem('ATP_token')
    const thisMonth = getThisMonth()
    const thisYear = getThisYear()

    if (token !== null) {
        await axios.post(process.env.REACT_APP_API_URL + "/api/tasksbyuser", {
                'email': getEmailFromJWT()
            }, {
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            })
            .then(response => {
                if (response.status === 200 && response.data.length > 0) {

                    if (month === -1 && year === -1 && clientId === -1) {
                        response.data.forEach(task => {
                            let date = new Date(task.date)
                            // let nextTaskDate = new Date(task[i + 1].date)
                            if (thisMonth === date.getMonth() && thisYear === date.getFullYear()) {
                                monthTotal += task.totalCost
                            }
                        })
                    } else if (clientId === 0) {

                        response.data.forEach(task => {
                            let date = new Date(task.date)
                            // new Date is 1 month late
                            if (month === (date.getMonth() + 1) && year === date.getFullYear()) {

                                monthTotal += task.totalCost
                            }

                        })
                    } else if (month === 0 && year === 0) {
                        response.data.forEach(task => {
                            // new Date is 1 month late
                            if (clientId === task.client.id) {
                                monthTotal += task.totalCost
                            }

                        })
                    } else {
                        response.data.forEach(task => {
                            let date = new Date(task.date)
                            // new Date is 1 month late
                            if (month === (date.getMonth() + 1) && year === date.getFullYear() && clientId === task.client.id) {

                                monthTotal += task.totalCost
                            }

                        })

                    }
                }
            }).catch((error) => {
                console.log(error)
            })
    }
    return monthTotal

}

let getMonthHours = async (month = -1, year = -1, clientId = -1) => {
    let monthTotal = "00:00"
    const token = localStorage.getItem('ATP_token')
    const thisMonth = getThisMonth()
    const thisYear = getThisYear()

    if (token !== null) {
        await axios.post(process.env.REACT_APP_API_URL + "/api/tasksbyuser", {
                'email': getEmailFromJWT()
            }, {
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            })
            .then(response => {
                if (response.status === 200 && response.data.length > 0) {

                    if (month === -1 && year === -1 && clientId === -1) {
                        response.data.forEach(task => {
                            let date = new Date(task.date)
                            // let nextTaskDate = new Date(task[i + 1].date)
                            if (thisMonth === date.getMonth() && thisYear === date.getFullYear()) {
                                monthTotal = timeAddSub(task.TotalHours, monthTotal)
                            }
                        })
                    } else if (clientId === 0) {

                        response.data.forEach(task => {
                            let date = new Date(task.date)
                            // new Date is 1 month late
                            if (month === (date.getMonth() + 1) && year === date.getFullYear()) {
                                monthTotal = timeAddSub(task.TotalHours, monthTotal)
                            }
                        })
                    } else if (month === 0 && year === 0) {
                        response.data.forEach(task => {
                            // new Date is 1 month late
                            if (clientId === task.client.id) {
                                monthTotal = timeAddSub(task.TotalHours, monthTotal)
                            }

                        })
                    } else {
                        response.data.forEach(task => {
                            let date = new Date(task.date)
                            // new Date is 1 month late
                            if (month === (date.getMonth() + 1) && year === date.getFullYear() && clientId === task.client.id) {
                                monthTotal = timeAddSub(task.TotalHours, monthTotal)
                            }
                        })
                    }

                }
            }).catch((error) => {
                console.log(error)
            })
    }

    // console.log(monthTotal)
    return monthTotal
}


let getRate = async () => {

    const token = localStorage.getItem('ATP_token')
    const userId = localStorage.getItem('ATP_userId')


    let rate = {}

    if (token !== null) {
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
    }

    return rate
}


let getMonthNameFromNumber = (number) => {

    let name = ''
    switch (number) {
        case 1:
            name = "Januari"
            break;

        case 2:
            name = "Fabuari"
            break;
        case 3:
            name = "Maart"
            break;
        case 4:
            name = "April"
            break;
        case 5:
            name = "Mei"
            break;
        case 6:
            name = "Juni"
            break;
        case 7:
            name = "Juli"
            break;
        case 8:
            name = "Augustus"
            break;
        case 9:
            name = "September"
            break;
        case 10:
            name = "Oktober"
            break;
        case 11:
            name = "November"
            break;
        case 12:
            name = "December"
            break;

        default:
            break;
    }

    return name
}

export {
    getMonthIncome,
    getMonthHours,
    getRate,
    getMonthNameFromNumber,
    getMonthAndYear
}