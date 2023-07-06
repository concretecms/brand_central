import React, { Component } from 'react'
import Select from "react-select"

// let current = null

class SelectTypeDropdown extends Component {
    
    render (){

        const options = [{ value: 'photo', label: 'Photo' },
        { value: 'logo', label: 'Logo' },
        { value: 'video', label: 'Video' },
        { value: 'template', label: 'Template' }]

        const handleChange = (payload) => {
            this.props.onValueChange(payload.value)
        }
        
        return (
            <div>
                <Select 
                    defaultValue={options[0]} 
                    options={options} 
                    onChange={handleChange}
                    classNamePrefix="select"
                    filterOption={false}
                    isSearchable={false}
                />
            </div>
        )
    }
}


export default SelectTypeDropdown