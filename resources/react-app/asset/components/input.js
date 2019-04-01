import React from "react"
import {connect} from 'react-redux'
import {setInput} from '../actions/assetActions'

const Input = (props) => {
    
    const field = props.field

    return (
        <div>
            <label>{props.label} <span>{props.required ? "*" : "" }</span></label>
            <div className={props.error ? "error-warning":''}>
                <input value={props.asset[field]} onChange={props.inputChanged} onBlur={(event)=>props.focusOut(event)} />
            </div>
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        asset:state.asset,
        app:state.app
    }
}

const mapDispatchToProps = (dispatch, props) => {
    return {
        inputChanged : (event) => dispatch(setInput(props.action,event.target.value)),
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(Input)