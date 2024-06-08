module.exports = {
    testEnvironment: 'jest-environment-jsdom',
    transform: {
        '^.+\\.vue$': '@vue/vue3-jest',
        '^.+\\.[t|j]sx?$': 'babel-jest',
    },
    moduleFileExtensions: ['js', 'jsx', 'json', 'vue'],
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/resources/js/$1',
        "^@vue/test-utils": "<rootDir>/node_modules/@vue/test-utils/dist/vue-test-utils.cjs.js",
    },
    transformIgnorePatterns: ['/node_modules/'],
};

