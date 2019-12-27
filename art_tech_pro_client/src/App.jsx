import React from 'react'
import './App.sass'

import ATPRouter from './router'
import { createStore } from 'redux'
import { Provider } from 'react-redux'


let reducer = () => {
  return {
    nickname: "Tendarr"
  }
}

const store = createStore(reducer)


function App() {
  return (
    <Provider store={store}>

      <ATPRouter />

    </Provider>
  )
}

export default App
