import React from 'react'
import './App.sass'

import ATPRouter from './router'
import { createStore } from 'redux'
import { Provider } from 'react-redux'
import rootReducer from './store/reducers'


const store = createStore(rootReducer,
  window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__())


function App() {
  return (
    <Provider store={store}>

      <ATPRouter />

    </Provider>
  )
}

export default App
