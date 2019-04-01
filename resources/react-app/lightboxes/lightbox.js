import '@babel/polyfill'
import React from 'react'
import ReactDOM from 'react-dom'
import App from './containers/app'



class Lightbox extends React.Component {
    run(id) {
        this.setState(() => ({currentAsset:id, showModal:true}))
    }
    close() {
        this.setState(() => ({currentAsset:null, showModal:false}))
    }
    constructor() {
        super()
        this.state = {
            currentAsset:null,
            showModal:false
        }
    }

    render () {
        return (
            <div className="lightbox-container">
                <App label="Lightbox" asset={this.state.currentAsset} showModal={this.state.showModal} closeModal={() => this.close()}/>
            </div>
        )
    }
}

ReactDOM.render(<Lightbox ref={lightbox => { window.lightbox = lightbox }}/>, document.getElementById('lightbox-app'))