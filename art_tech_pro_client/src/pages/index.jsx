import React from 'react';
import BaseLayout from '../layouts/base';
import Button from '../components/Button'
let HomePage = () => {

    return (
        <div>
            <div className="title__main">Totaal uren gewerkt deze maand</div>
            <div className="text">561651 UUR </div>
            <div className="title__main">Totaal verdiende deze maand</div>
            <div className="text">908900 EUR </div>
            <hr />

            <Button name="PAS AAN" type="main" />
        </div>
    )
}

export default BaseLayout(HomePage)