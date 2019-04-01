import React, { Component } from 'react'
import Select from "react-select"


const customStyle = {
    control: styles => ({
        ...styles,
        backgroundColor: '#ffffff',
        borderRadius: 0,
        height:'30px',
        boxShadow:'none',
        borderColor:'#f4f4f4'
    })
}

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
                    styles={customStyle} 
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