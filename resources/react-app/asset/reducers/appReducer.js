const InitialState = {
    isLoading:true,
    selectTypeOptions: [
        { value: 'photo', label: 'Photo' },
        { value: 'logo', label: 'Logo' },
        { value: 'video', label: 'Video' },
        { value: 'template', label: 'Template' }
    ],
    thumbnail:{
        id: null,
        isLoading:false
    },
    tagsGenerator: {
        isLoading:false,
        isBtnVisible:true
    },
    modal: {
        isVisible:false
    },
    currentFile: {
        id: null,
        fid: null,
        desc: '',
        img: ''
    },
    errorName:false,
    errorType:false,
    errorCollections:false,
    errorFiles:false,
}

const appReducer = (
    state = InitialState, action) => {
    switch (action.type){
        case "SET_IS_LOADING":
            return Object.assign({}, state, {
                isLoading: action.payload    
            })
        case "UPDATE_THUMBNAIL_ID":
            return Object.assign({}, state, {
                thumbnail: {id:action.payload}
            })
        case "UPDATE_THUMBNAIL_LOADING":
            return Object.assign({}, state, {
                thumbnail: {isLoading:action.payload}
            })
        case "SET_GENTAGS_IS_LOADING":
            return Object.assign({}, state, {
                tagsGenerator: {isLoading:action.payload}
            })
        case "SET_GENTAGS_BTN_IS_VISIBLE":
            return Object.assign({}, state, {
                tagsGenerator: {isBtnVisible:action.payload}
            })
        case "SET_MODAL_VISIBILITY":
            return Object.assign({}, state, {
                modal: {isVisible:action.payload}
            })
        case "SET_CURRENT_SELECTED_FILE":
            return Object.assign({}, state, {
                currentFile: {
                    id:action.payload.id,
                    fid: action.payload.fid,
                    desc: action.payload.desc,
                    filename:action.payload.filename,
                    img: action.payload.img
                }
            })
        case "UPDATE_CURRENT_SELECTED_FILE_DESC":
            return Object.assign({}, state, {
                currentFile: Object.assign({}, state.currentFile, {
                    desc:action.payload
                })
            })
        case "SET_ERROR_NAME":
            return Object.assign({}, state, {
                errorName: action.payload    
            })
        case "SET_ERROR_TYPE":
            return Object.assign({}, state, {
                errorType: action.payload    
            })
        case "SET_ERROR_COLLECTIONS":
            return Object.assign({}, state, {
                errorCollections: action.payload    
            })
        case "SET_ERROR_FILES":
            return Object.assign({}, state, {
                errorFiles: action.payload    
            })
        default: return state
    }
}

export default appReducer
