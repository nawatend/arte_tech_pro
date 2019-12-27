import React from 'react';
import Nav from '../components/Nav'
import Header from '../components/Header'
const BaseLayout = (Page) => {

    return () => (
        <div className="App background__main">
            <Nav />
            <Header />
            <main className="main__content">
                <Page />
            </main>
        </div>
    )

}

BaseLayout.displayName = "Base Layout"
export default BaseLayout