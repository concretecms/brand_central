import React, {Component} from 'react'
import AsyncCreatableSelect from 'react-select/lib/AsyncCreatable'
import {connect} from 'react-redux'
import {runAction} from '../actions/assetActions'
import {transactionId} from "../lib/assetServices"
import _ from 'underscore'
import axios from 'axios'


const customStyle = {
    control: styles => ({
        ...styles,
        backgroundColor: '#ffffff',
        borderRadius: 0,
        height: '45px',
        boxShadow: 'none',
        borderColor: '#ccc',
    })
}

class SelectDropdown extends Component {

    constructor(props) {
        super(props);
        this.state = {value: '', isFocused:false};
        this.dropRef = React.createRef()
    }

    render() {

        const addItem = (payload) => {
            const utid = transactionId();
            let item = {id: utid, name: payload.label}
            this.props.inputChanged(item)
            // console.log(this.dropRef)
        }

        const getOptions = (inputValue) => {

            if (!this.debouncer) {
                let me = this;
                this.promise = new Promise(function (resolve, reject) {
                    me.resolve = resolve;
                });

                this.debouncer = _.debounce(function (inputValue) {
                    this.promise = null;
                    this.debouncer = null;
                    axios.get(CCM_DISPATCHER_FILENAME + `/api/v1/${this.props.field}?search=${inputValue}`)
                        .then(response => {
                            me.resolve(response.data.map(option => ({value: option.id, label: option.name})))
                        })
                }, 600);
            }

            this.debouncer(inputValue);
            return this.promise;
        }


        return (
            <div>
                <label>{this.props.label} <span>{this.props.required ? "*" : null}</span></label>
                <AsyncCreatableSelect
                    ref={this.dropRef}
                    value={this.state.value}
                    styles={customStyle}
                    classNamePrefix="select"
                    loadOptions={getOptions}
                    defaultOptions={this.props.dropdown}
                    onChange={addItem}
                    isValidNewOption={(inputValue, selectValue, selectOptions) => {
                        const isNotDuplicated = !selectOptions
                            .map(option => option.label)
                            .includes(inputValue);
                        const isNotEmpty = inputValue !== '';
                        return isNotEmpty && isNotDuplicated;
                    }}
                    autofocus
                />
            </div>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        app: state.asset
    }
}

const mapDispatchToProps = (dispatch, props) => {
    return {
        inputChanged: (payload) => dispatch(runAction(props.actionReducer, payload))
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(SelectDropdown)