const InitialState = {
    id: null,
    name:"",
    desc:"",
    location:"",
    type:"",
    files:[],
    thumbnail:"",
    thumbnailId:null,
    tags:[],
    collections:[]
}

const assetReducer = ( state = InitialState, action) => {
    switch (action.type){
        case "LOAD_ASSET":
            return Object.assign({}, state, {
                ...action.payload
            })
        case "SET_ASSET_ID":
            return Object.assign({}, state, {
                id: action.payload
            })
        case "SET_ASSET_NAME":
            return Object.assign({}, state, {
                name: action.payload
            })
        case "SET_ASSET_DESC":
            return Object.assign({}, state, {
                desc: action.payload
            })
        case "SET_ASSET_TYPE":
            return Object.assign({}, state, {
                type: action.payload
            })
        case "SET_ASSET_LOCATION":
            return Object.assign({}, state, {
                location: action.payload
            })
        case "SET_ASSET_THUMB":
            return Object.assign({}, state, {
                thumbnail: action.payload
            })
        case "SET_ASSET_THUMB_ID":
            return Object.assign({}, state, {
                thumbnailId: action.payload
            })
        case "SET_ASSET_FILES":
            return Object.assign({}, state, {
                files: state.files.concat({
                    id: action.payload.id,
                    desc: action.payload.desc,
                    filename: action.payload.filename,
                    img: action.payload.img,
                    isLoading: action.payload.isLoading
                })
            })
        case "UPDATE_ASSET_FILE":
            return Object.assign({}, state, {
                files: state.files.map(file => {
                    if(file.id !== action.payload.id){
                        return file
                    }
                    return Object.assign({}, file, {
                        isLoading:action.payload.isLoading,
                        fid: action.payload.fid,
                        img: action.payload.img,
                        desc: action.payload.desc,
                        errorMsg: action.payload.errorMsg,
                        hasErrors: action.payload.hasErrors
                    })
                })
            })
        case "REMOVE_ASSET_FILE":
            return Object.assign({}, state, {
                files: state.files.filter(item => item.id !== action.payload)
            })
        case "SET_ASSET_COLLECTIONS":
            if (!action.payload.name || !action.payload.name.trim()) {
                return state;
            }

            let collectionMatch = state.collections.filter(function (tag, key) {
                return tag.name.toLowerCase().trim() === action.payload.name.toLowerCase().trim();
            }).length;

            if (collectionMatch) {
                return state;
            }
            return Object.assign({}, state, {
                collections: state.collections.concat({
                    id:action.payload.id,
                    name:action.payload.name
                })
            })
        case "SET_ASSET_TAGS":
            if (!action.payload.name || !action.payload.name.trim()) {
                return state;
            }

            let tagMatch = state.tags.filter(function (tag, key) {
                return tag.name.toLowerCase().trim() === action.payload.name.toLowerCase().trim();
            }).length;

            if (tagMatch) {
                return state;
            }

            return Object.assign({}, state, {
                tags: state.tags.concat({
                    id: action.payload.id,
                    name: action.payload.name
                })
            })
        case "REMOVE_TAG":
            return Object.assign({}, state, {
                tags: state.tags.filter(item => item.id !== action.payload)
            })
        case "REMOVE_COLLECTION":
            return Object.assign({}, state, {
                collections: state.collections.filter(item => item.id !== action.payload)
            })

        // Handle the asset beginning or ending saving. This simply triggers redraw and has no other pertinent effects.
        case "TOGGLE_SAVING":
            return Object.assign({}, state, {
                saving: action.payload
            });

        default: return state
    }
}

export default assetReducer
