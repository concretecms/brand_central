import React, { Component } from 'react'

class Input extends Component {

    render() {
        return (
            <div className="input-group">
                <input className="form-control create-lightbox-input" onChange={(e) => this.props.onValueChange(e.target.value)} autoFocus/>
                <span className="input-group-btn">
                    <button className="btn btn-defualt create-lightbox-btn" onClick={()=>this.props.saveEntry()}>Create</button>
                </span>
            </div>
        )
    }

}

export default Input