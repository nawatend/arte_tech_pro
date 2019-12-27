import React from 'react'

const AuthLayout = (Page) => {

    return () => (
        <div className="App auth__background__main">
            <div className="auth__header">
                <div className="image__logo">
                    <img src="./logo.png" alt="logo of arte tech pro" />
                    <div className="auth__slogan">Yes, we do have a web app</div>
                </div>
            </div>
            <main className="auth__main__content">
                <Page />
            </main>
        </div>
    )

}

AuthLayout.displayName = "Auth Layout"
export default AuthLayout