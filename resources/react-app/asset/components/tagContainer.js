import React, {Component} from 'react'

class TagContainer extends Component {

    render () {
        const tags = this.props.tags.map(tag => 
            <span className="round-tag-span" key={tag.id}>
                {tag.name}  
                <span onClick={()=>this.props.removeTag(tag.id,null)} className="remove-tag-btn"> <i className="fas fa-times"></i> </span>
            </span>)

        return (
            <section>
                {tags}
            </section>
        )
    }
}
export default TagContainer