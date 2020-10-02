import React, {Component} from 'react'
import AsyncCreatableSelect from 'react-select/lib/AsyncCreatable'
import {connect} from 'react-redux'
import {runAction} from '../actions/assetActions'
import _ from 'underscore'
import axios from 'axios'

const customStyle = {
    control: styles => ({
        ...styles,
        backgroundColor: '#ffffff',
        borderRadius: 0,
        height: '45px',
        boxShadow: 'none',
        borderColor: '#ccc'
    })
}

class SelectCollections extends Component {
    constructor(props) {
        super(props);
        this.state = {
            value: ''
        };
    }

    render() {

        const addItem = (payload) => {
            let item = {id: payload.value, name: payload.label}
            this.props.inputChanged(item)
            this.props.setError(false)
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

                    axios.get(CCM_DISPATCHER_FILENAME + `/api/v1/collections?search=${inputValue}`)
                        .then(response => {
                            me.resolve(response.data.map(option => ({value: option.id, label: option.name})));
                        })
                }, 600);
            }

            this.debouncer(inputValue);
            return this.promise;
        }

        const handleCreate = (inputValue) => {
            console.log(inputValue);
            axios.post(CCM_DISPATCHER_FILENAME + "/api/v1/collections/create", {collection: inputValue}, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    const optionId = response.data.collection
                    const newOption = {id: optionId, name: inputValue}
                    this.props.inputChanged(newOption)
                })
        }

        return (
            <div>
                <label>{this.props.label} <span>{this.props.required ? "*" : null}</span></label>
                <div className={this.props.app.errorCollections ? 'error-warning' : null}>
                    <AsyncCreatableSelect
                        value={this.state.value}
                        styles={customStyle}
                        classNamePrefix="select"
                        loadOptions={getOptions}
                        defaultOptions
                        onChange={addItem}
                        onCreateOption={handleCreate}
                        isValidNewOption={(inputValue, selectValue, selectOptions) => {
                            const isNotDuplicated = !selectOptions
                                .map(option => option.label)
                                .includes(inputValue);
                            const isNotEmpty = inputValue !== '';
                            return isNotEmpty && isNotDuplicated;
                        }}
                    />
                </div>
            </div>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        app: state.app
    }
}

const mapDispatchToProps = (dispatch, props) => {
    return {
        inputChanged: (payload) => dispatch(runAction(props.actionReducer, payload)),
        setError: (payload) => dispatch(runAction("SET_ERROR_COLLECTIONS", payload))
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(SelectCollections)